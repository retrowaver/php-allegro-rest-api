<?php
namespace Allegro\REST\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Http\Client\HttpClient;
use Allegro\REST\Middleware\Middleware\BaseMiddleware;
use Allegro\REST\Middleware\Middleware\ImageUploadMiddleware;

class HttplugMiddlewareDecorator implements HttpClient
{
    protected $client;

    public function __construct(HttpClient $client, array $queue)
    {
        $this->client = $client;
        $this->queue = array_merge(
            $queue,
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
        reset($this->queue);

        $runner = new Runner($this->queue);
        return $runner->handle($request);
    }
}
