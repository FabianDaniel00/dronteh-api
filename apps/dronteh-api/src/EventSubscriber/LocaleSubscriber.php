<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LocaleSubscriber implements EventSubscriberInterface
{
    private ParameterBagInterface $params;
    private TokenStorageInterface $tokenStorage;

    public function __construct(ParameterBagInterface $params, TokenStorageInterface $tokenStorage) {
        $this->params = $params;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        $localeSuggestions = [
            explode('/', $request->getPathInfo())[1],
            $request->attributes->get('_locale'),
            $request->query->get('locale'),
            $request->headers->get('locale'),
        ];

        foreach ($localeSuggestions as $locale) {
            if ($locale && in_array($locale, explode('|', $this->params->get('app.supported_locales')))) {
                if ($request->hasPreviousSession()) {
                    $request->getSession()->set('_locale', $locale);
                }

                return $request->setLocale($locale);
            }
        }

        if ($request->hasPreviousSession()) {
            return $request->setLocale($request->getSession()->get('_locale', $this->params->get('app.default_locale')));
        }

        if ($this->tokenStorage->getToken() && $currentUser = $this->tokenStorage->getToken()->getUser()) {
            return $request->setLocale($currentUser->getLocale());
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 40]],
        ];
    }
}
