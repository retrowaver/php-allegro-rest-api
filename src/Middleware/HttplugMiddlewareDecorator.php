<?php
namespace Allegro\REST\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Http\Client\HttpClient;
use Allegro\REST\Middleware\Middleware\BaseMiddleware;
use Allegro\REST\Middleware\Middleware\ImageUploadMiddleware;

/**
 * HTTPlug middleware decorator
 * 
 * Decorator that introduces middleware for HTTPlug client
 * Inspired by PSR-15 and https://github.com/relayphp/Relay.Relay.
 */
class HttplugMiddlewareDecorator implements HttpClient
{
    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * @var MiddlewareInterface[]
     */
    protected $middlewares;

    public function __construct(HttpClient $client, array $middlewares = [])
    {
        $this->client = $client;
        $this->middlewares = array_merge(
            $middlewares,
            [
                new ImageUploadMiddleware,
                new BaseMiddleware($client)
            ]
        );
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->handle($request);
    }

    protected function handle(RequestInterface $request): ResponseInterface
    {
        reset($this->middlewares);
        $runner = new Runner($this->middlewares);
        return $runner->handle($request);
    }
}
