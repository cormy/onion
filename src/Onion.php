<?php

namespace Cormy\Server;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Onion style PSR-7 middleware stack.
 */
class Onion implements RequestHandlerInterface
{
    /**
     * @var callable|RequestHandlerInterface
     */
    protected $core;

    /**
     * @var callable[]|MiddlewareInterface[]
     */
    protected $scales;

    /**
     * Constructs an onion style PSR-7 middleware stack.
     *
     * @param callable|RequestHandlerInterface $core   the innermost request handler
     * @param callable[]|MiddlewareInterface[] $scales the middlewares to wrap around the core
     */
    public function __construct(callable $core, callable ...$scales)
    {
        $this->core = $core;
        $this->scales = $scales;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request):ResponseInterface
    {
        $topIndex = count($this->scales) - 1;

        return $this->processMiddleware($topIndex, $request);
    }

    /**
     * Process an incoming server request by delegating it to the middleware specified by $index.
     *
     * @param int                    $index   the $scales index
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    protected function processMiddleware(int $index, ServerRequestInterface $request):ResponseInterface
    {
        if ($index < 0) {
            return ($this->core)($request);
        }

        $current = $this->scales[$index]($request);
        $nextIndex = $index - 1;

        while ($current->valid()) {
            $nextRequest = $current->current();

            try {
                $nextResponse = $this->processMiddleware($nextIndex, $nextRequest);
                $current->send($nextResponse);
            } catch (Throwable $exception) {
                $current->throw($exception);
            }
        }

        return $current->getReturn();
    }
}
