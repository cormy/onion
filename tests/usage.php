#!/usr/bin/env php
<?php

namespace Cormy;

require __DIR__.'/../vendor/autoload.php';

use Generator;
use Cormy\Server\Onion;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

// create the innermost request handler
$core = function (ServerRequestInterface $request):ResponseInterface {
    return new Response();
};

// create your middlewares
$scales = [
    function (ServerRequestInterface $request):Generator {
        // delegate $request to the next request handler, i.e. $core
        $response = yield $request;

        // mofify the response
        $response = $response->withHeader('content-type', 'application/json; charset=utf-8');

        return $response;
    },
    function (ServerRequestInterface $request):Generator {
        // delegate $request to the next request handler, i.e. the middleware right above
        $response = yield $request;

        // mofify the response
        $response = $response->withHeader('X-PoweredBy', 'Unicorns');

        return $response;
    },
];

// create the onion style stack
$middlewareStack = new Onion($core, ...$scales);

// and dispatch it
$response = $middlewareStack(new ServerRequest());

exit($response->getHeader('X-PoweredBy')[0] === 'Unicorns' ? 0 : 1);
