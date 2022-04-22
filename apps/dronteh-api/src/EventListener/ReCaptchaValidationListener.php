<?php

namespace App\EventListener;

use ReCaptcha\ReCaptcha;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ReCaptchaValidationListener implements EventSubscriberInterface
{
    private ReCaptcha $reCaptcha;
    private TranslatorInterface $translator;

    public function __construct(ReCaptcha $reCaptcha, TranslatorInterface $translator)
    {
        $this->reCaptcha = $reCaptcha;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit'
        ];
    }

    public function onPostSubmit(FormEvent $event)
    {
        $request = Request::createFromGlobals();

        $result = $this->reCaptcha
            ->setExpectedHostname($request->getHost())
            ->verify($request->request->get('g-recaptcha-response'), $request->getClientIp());

        if (!$result->isSuccess()) {
            $event->getForm()->addError(new FormError($this->translator->trans('app.register.captcha', [], 'app')));
        }
    }
}
