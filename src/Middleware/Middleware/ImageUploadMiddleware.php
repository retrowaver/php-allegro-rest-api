<?php
namespace Retrowaver\Allegro\REST\Middleware\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Retrowaver\Allegro\REST\Middleware\MiddlewareInterface;
use Retrowaver\Allegro\REST\Middleware\RequestHandlerInterface;

/**
 * Image upload middleware
 * 
 * Middleware that handles a unique case of image upload request
 * (URI host is changed, while the rest remains the same).
 */
class ImageUploadMiddleware implements MiddlewareInterface
{
    public function process(
        RequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $path = $request->getUri()->getPath();
    
        if ($path !== '/sale/images') {
            return $handler->handle($request);
        }

        return $handler->handle(
            $request
                ->withUri(
                    $request->getUri()->withHost(
                        str_replace(
                            'api.allegro.pl',
                            'upload.allegro.pl',
                            $request->getUri()->getHost()
                        )
                    )
                )
        );
    }
}
