<?php

namespace App\EventSubscriber;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    private Environment $engine;
    private KernelInterface $kernel;
    private TranslatorInterface $translator
    ;

    public function __construct(Environment $engine, KernelInterface $kernel, TranslatorInterface $translator)
    {
        $this->engine = $engine;
        $this->kernel = $kernel;
        $this->translator = $translator;
    }

    public function httpException(ExceptionEvent $event)
    {
        $pathInfo = explode('/', $event->getRequest()->getPathInfo());

        if ($this->kernel->getEnvironment() !== 'dev' && $pathInfo && $pathInfo[1] !== 'api') {
            return $this->handleNonApiException($event);
        }

        if ($event->getThrowable() instanceof NotFoundHttpException) {
            $file = explode('/', $event->getThrowable()->getFile());
            if ($file && $file[count($file) - 1] === 'DoctrineParamConverter.php') {
                try {
                    $entityName = $pathInfo[2];
                } catch (\Exception $e) {
                    $entityName = 'entity';
                }
                return $event->setThrowable(new NotFoundHttpException($this->translator->trans('api.'.$entityName.'.not_found', [], 'api')));
            }
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

    private function handleNonApiException(ExceptionEvent $event)
    {
        $responseCode = 500;
        $throwable = $event->getThrowable();

        if ($throwable instanceof NotFoundHttpException) {
            $responseCode = 404;
        }

        if ($throwable instanceof AccessDeniedHttpException) {
            $responseCode = 403;
        }

        $event->setResponse(new Response($this->engine->render('exception/error.html.twig', [
            'message' => $event->getThrowable()->getMessage(),
            'responseCode' => $responseCode,
            'locale' => $event->getRequest()->getLocale(),
        ])));
    }
}
