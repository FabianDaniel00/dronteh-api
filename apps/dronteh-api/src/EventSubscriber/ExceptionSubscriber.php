<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function httpException(ExceptionEvent $event)
    {
        if ($event->getThrowable() && $event->getThrowable()->getStatusCode() === Response::HTTP_NOT_FOUND) {
            $entityName = explode('/', $event->getRequest()->getPathInfo());
            $event->setThrowable(new NotFoundHttpException('api.'.$entityName[2].'.not_found'));
        }
    }

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            KernelEvents::EXCEPTION => [
                ['httpException'],
            ],
        ];
    }
}
