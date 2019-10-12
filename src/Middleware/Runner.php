<?php
namespace Allegro\REST\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Runner extends RequestHandler
{
    public function handle(RequestInterface $request) : ResponseInterface
    {
        $middleware = current($this->queue);
        next($this->queue);
        
        return $middleware->process($request, $this);
    }
}
