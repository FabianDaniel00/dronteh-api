<?php

namespace App\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ComparisonType extends AbstractType
{
    public const EQ = '=';
    public const NEQ = '!=';
    public const GT = '>';
    public const GTE = '>=';
    public const LT = '<';
    public const LTE = '<=';
    public const BETWEEN = 'between';
    public const IS_NULL = 'is null';

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                'filter.label.is_same' => self::EQ,
                'filter.label.is_not_same' => self::NEQ,
                'filter.label.is_after' => self::GT,
                'filter.label.is_after_or_same' => self::GTE,
                'filter.label.is_before' => self::LT,
                'filter.label.is_before_or_same' => self::LTE,
                'filter.label.is_between' => self::BETWEEN,
                'label.null' => self::IS_NULL,
            ],
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
