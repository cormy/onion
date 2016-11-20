<?php

namespace Cormy\Server\Helpers;

use Generator;
use Cormy\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

class MultiDelegationMiddleware implements MiddlewareInterface
{
    protected $count;

    public function __construct(int $count)
    {
        $this->count = $count;
    }

    public function __invoke(ServerRequestInterface $request) : Generator
    {
        for ($i = 0; $i < $this->count; ++$i) {
            $response = (yield $request);
        }

        return $response;
    }
}
