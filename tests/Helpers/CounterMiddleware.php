<?php

namespace Cormy\Server\Helpers;

use Generator;
use Cormy\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

class CounterMiddleware implements MiddlewareInterface
{
    protected $index;

    public function __construct(int $index = 0)
    {
        $this->index = $index;
    }

    public function __invoke(ServerRequestInterface $request) : Generator
    {
        $response = yield $request;
        $response->getBody()->write((string) $this->index++);

        return $response;
    }
}
