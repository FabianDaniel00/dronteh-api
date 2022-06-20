<?php

namespace App\Form\Type;

use ReCaptcha\ReCaptcha;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use App\EventListener\ReCaptchaValidationListener;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReCaptchaType extends AbstractType
{
    /**
     * @var ReCaptcha
     */
    private ReCaptcha $reCaptcha;
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * ReCaptchaType constructor.
     *
     * @param ReCaptcha $reCaptcha
     */
    public function __construct(ReCaptcha $reCaptcha, TranslatorInterface $translator)
    {
        $this->reCaptcha = $reCaptcha;
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new ReCaptchaValidationListener($this->reCaptcha, $this->translator));
    }

	/**
     * @inheritDoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['type'] = $options['type'];
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('type', 'invisible')
            ->setAllowedValues('type', ['checkbox', 'invisible'])
		;
    }
}
