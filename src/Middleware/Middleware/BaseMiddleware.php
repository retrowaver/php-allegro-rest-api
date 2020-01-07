<?php
namespace Retrowaver\Allegro\REST\Middleware\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Retrowaver\Allegro\REST\Middleware\MiddlewareInterface;
use Http\Client\HttpClient;
use Retrowaver\Allegro\REST\Middleware\RequestHandlerInterface;

/**
 * Base middleware
 * 
 * Deepest middleware that sends a request to API and returns a response.
 */
class BaseMiddleware implements MiddlewareInterface
{
    /**
     * @var HttpClient
     */
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
