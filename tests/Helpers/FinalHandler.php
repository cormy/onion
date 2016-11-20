<?php

namespace Cormy\Server\Helpers;

use Cormy\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FinalHandler implements RequestHandlerInterface
{
    protected $body;

    public function __construct(string $body)
    {
        $this->body = $body;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ServerRequestInterface $request):ResponseInterface
    {
        return new Response($this->body);
    }
}
