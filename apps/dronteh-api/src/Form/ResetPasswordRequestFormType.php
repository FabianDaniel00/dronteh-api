<?php

namespace App\Form;

use App\Form\Type\ReCaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ResetPasswordRequestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'validators.register.email.label',
                'attr' => [
                    'autocomplete' => 'email',
                    'placeholder' => ' ',
                ],
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ],
            ])
            // ->add('captcha', ReCaptchaType::class, [
            //     'type' => 'invisible', // (invisible, checkbox)
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'validators',
        ]);
    }
}
