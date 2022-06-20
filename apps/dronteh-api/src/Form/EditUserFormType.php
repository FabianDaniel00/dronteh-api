<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EditUserFormType extends AbstractType
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locales = [];
        foreach (explode('|', $this->params->get('app.supported_locales')) as $locale) {
            $locales['validators.locales.' . $locale] = $locale;
        }

        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => ' ',
                ],
                'label' => 'validators.register.email.label',
            ])
            ->add('firstname', TextType::class, [
                'attr' => [
                    'placeholder' => ' ',
                ],
                'label' => 'validators.register.firstname.label',
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'placeholder' => ' ',
                ],
                'label' => 'validators.register.lastname.label',
            ])
            ->add('tel', TelType::class, [
                'attr' => [
                    'placeholder' => ' ',
                ],
                'label' => 'validators.register.tel.label',
            ])
            ->add('locale', ChoiceType::class, [
                'label' => 'validators.register.locale.label',
                'choices' => $locales,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'validators',
        ]);
    }
}
