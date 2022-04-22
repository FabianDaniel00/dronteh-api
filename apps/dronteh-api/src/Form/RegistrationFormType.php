<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\Locales;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RegistrationFormType extends AbstractType
{
    private ParameterBagInterface $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $locales = [];
        foreach(explode('|', $this->params->get('app.supported_locales')) as $locale) {
            $locales['validators.locales.'.$locale] = $locale;
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
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => ' ',
                    ],
                    'label' => 'validators.register.plain_password.first_options.label',
                ],
                'second_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => ' ',
                    ],
                    'label' => 'validators.register.plain_password.second_options.label',
                ],
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'invalid_message' => 'validators.register.plain_password.not_match'
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
            ])
            ->add('captcha', ReCaptchaType::class, [
                'type' => 'invisible', // (invisible, checkbox)
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'translation_domain' => 'validators',
        ]);
    }
}
