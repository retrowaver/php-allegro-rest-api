<?php
namespace Retrowaver\Allegro\REST\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class RequestHandler implements RequestHandlerInterface
{
    protected $queue;

    public function __construct(array $queue)
    {
        $this->queue = $queue;
    }

    abstract public function handle(RequestInterface $request): ResponseInterface;
}
