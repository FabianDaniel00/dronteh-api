<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LocaleSubscriber implements EventSubscriberInterface
{
    private $params;

    public function __construct(ParameterBagInterface $params) {
        $this->params = $params;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        $locale = $request->query->get('locale');
        if ($locale && in_array($locale, $this->params->get('app.supported_locales'))) {
            return $request->setLocale($locale);
        }

        $locale = $request->headers->get('locale');
        if ($locale && in_array($locale, $this->params->get('app.supported_locales'))) {
            return $request->setLocale($locale);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
