<?php

namespace App\Form\Type\Admin;

use App\Form\Type\Admin\ComparisonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ComparisonFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('comparison', ComparisonType::class);
        $builder->add('value', DateTimeType::class, ['widget' => 'single_text']);
    }
}
