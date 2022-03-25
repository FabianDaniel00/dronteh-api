<?php

namespace App\Form\Type\Admin;

use App\Form\Type\Admin\ComparisonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DateTimeFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('value2', DateTimeType::class, ['widget' => 'single_text']);

        $builder->addModelTransformer(new CallbackTransformer(
            static function ($data) {
                return $data;
            },
            static function ($data) {
                if (ComparisonType::BETWEEN === $data['comparison']) {
                    if (null === $data['value'] || '' === $data['value'] || null === $data['value2'] || '' === $data['value2']) {
                        throw new TransformationFailedException('Two values must be provided when "BETWEEN" comparison is selected.');
                    }

                    // make sure end datetime is greater than start datetime
                    if ($data['value'] > $data['value2']) {
                        [$data['value'], $data['value2']] = [$data['value2'], $data['value']];
                    }

                    if (DateType::class === DateTimeType::class) {
                        $data['value2'] = $data['value2']->format('Y-m-d');
                    } elseif (TimeType::class === DateTimeType::class) {
                        $data['value2'] = $data['value2']->format('H:i:s');
                    }
                }

                if ($data['value'] instanceof \DateTimeInterface) {
                    if (DateType::class === DateTimeType::class) {
                        // sqlite: Don't include time format for date comparison
                        $data['value'] = $data['value']->format('Y-m-d');
                    } elseif (TimeType::class === DateTimeType::class) {
                        // sqlite: Don't include date format for time comparison
                        $data['value'] = $data['value']->format('H:i:s');
                    }
                }

                return $data;
            }
        ));
    }

    public function getBlockPrefix(): string
    {
        return 'ea_datetime_filter';
    }

    public function getParent(): string
    {
        return ComparisonFilterType::class;
    }
}
