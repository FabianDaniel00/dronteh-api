<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => 'validators.change_password.plain_password.first_options.placeholder',
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'validators.change_password.plain_password.first_options.not_blank',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'validators.change_password.plain_password.first_options.length',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'label' => 'validators.change_password.plain_password.first_options.label',
                ],
                'second_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => 'validators.change_password.plain_password.second_options.placeholder',
                    ],
                    'label' => 'validators.change_password.plain_password.second_options.label',
                ],
                'translation_domain' => 'validators',
                'invalid_message' => 'validators.change_password.plain_password.invalid_message',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
