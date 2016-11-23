# Cormy Onion [![Build Status](https://travis-ci.org/cormy/onion.svg?branch=master)](https://travis-ci.org/cormy/onion) [![Coverage Status](https://coveralls.io/repos/cormy/onion/badge.svg?branch=master&service=github)](https://coveralls.io/github/cormy/onion?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/cormy/onion/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/cormy/onion/?branch=master)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/47e283f3-2eaf-4816-b75a-558dd0802bdd/big.png)](https://insight.sensiolabs.com/projects/47e283f3-2eaf-4816-b75a-558dd0802bdd)

> :tulip: Onion style [PSR-7](http://www.php-fig.org/psr/psr-7) **middleware stack** using generators


## Install

```
composer require cormy/onion
```


## Usage

```php
use Cormy\Server\Onion;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// create the core of the onion, i.e. the innermost request handler
$core = function (ServerRequestInterface $request):ResponseInterface {
    return new \Zend\Diactoros\Response();
};

// create some scales (aka middlewares) to wrap around the core
$scales = [];

$scales[] = function (ServerRequestInterface $request):\Generator {
    // delegate $request to the next request handler, i.e. $core
    $response = (yield $request);

    return $response->withHeader('content-type', 'application/json; charset=utf-8');
};

$scales[] = function (ServerRequestInterface $request):\Generator {
    // delegate $request to the next request handler, i.e. the middleware right above
    $response = (yield $request);

    return $response->withHeader('X-PoweredBy', 'Unicorns');
};

// create an onion style middleware stack
$middlewareStack = new Onion($core, ...$scales);

// and process an incoming server request
$response = $middlewareStack(new \Zend\Diactoros\ServerRequest());
```


## API

### `Cormy\Server\Onion implements RequestHandlerInterface`

#### `Onion::__construct`

```php
/**
 * Constructs an onion style PSR-7 middleware stack.
 *
 * @param callable|RequestHandlerInterface $core   the innermost request handler
 * @param callable[]|MiddlewareInterface[] $scales the middlewares to wrap around the core
 */
public function __construct(callable $core, callable ...$scales)
```

#### Inherited from [`RequestHandlerInterface::__invoke`](https://github.com/cormy/server-request-handler)

```php
/**
 * Process an incoming server request and return the response.
 *
 * @param ServerRequestInterface $request
 *
 * @return ResponseInterface
 */
public function __invoke(ServerRequestInterface $request):ResponseInterface
```


## Related

* [Cormy\Server\Bamboo](https://github.com/cormy/bamboo) – Bamboo style PSR-7 **middleware pipe** using generators
* [Cormy\Server\MiddlewareDispatcher](https://github.com/cormy/cormy/server-middleware-dispatcher) – Cormy PSR-7 server **middleware dispatcher**
* [Cormy\Server\RequestHandlerInterface](https://github.com/cormy/server-request-handler) – Common interfaces for PSR-7 server request handlers
* [Cormy\Server\MiddlewareInterface](https://github.com/cormy/server-middleware) – Common interfaces for Cormy PSR-7 server middlewares


## License

MIT © [Michael Mayer](http://schnittstabil.de)
