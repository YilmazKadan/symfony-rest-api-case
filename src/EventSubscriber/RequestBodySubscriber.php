<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RequestBodySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ('json' === $request->getContentType() && empty($request->getContent())) {
            throw new BadRequestHttpException('Boş gövde yollanamaz');
        }

        // Burada post veya put isteği yapıldığında ve contentType Json boş gövde gönderilmesini engelledim, amaç kullanıcı hatalarını yok etmek
        if ( $request->getMethod() === 'POST' || $request->getMethod() === 'PUT') {
            $data = json_decode($request->getContent(), true);
        
            if (($data === null || $data === []) && 'json' === $request->getContentType() ) {
                throw new BadRequestHttpException('Boş gövde yollanamaz');
            }
        }
        
    }
}
