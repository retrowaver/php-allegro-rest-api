<?php
namespace Allegro\REST\Middleware\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Allegro\REST\Middleware\MiddlewareInterface;
use Http\Client\HttpClient;
use Allegro\REST\Middleware\RequestHandlerInterface;

class BaseMiddleware implements MiddlewareInterface
{
    protected $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function process(
        RequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        return $this->client->sendRequest($request);
    }
}
