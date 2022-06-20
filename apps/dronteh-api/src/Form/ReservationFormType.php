<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Validator\DateRange;
use App\Form\Type\ReCaptchaType;
use App\Repository\PlantRepository;
use App\Repository\ChemicalRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ReservationFormType extends AbstractType
{
	private ChemicalRepository $chemicalRepository;
	private PlantRepository $plantRepository;
	private string $locale;

	public function __construct(ChemicalRepository $chemicalRepository, PlantRepository $plantRepository, TranslatorInterface $translator)
	{
		$this->chemicalRepository = $chemicalRepository;
		$this->plantRepository = $plantRepository;
		$this->locale = $translator->getLocale();
	}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
			$chemicals = [];
			foreach($this->chemicalRepository->findAll() as $chemical) {
				$chemicals[$chemical->{'getName'.ucfirst(str_replace('_', '', $this->locale))}()] = $chemical;
			}

			$plants = [];
			foreach($this->plantRepository->findAll() as $plant) {
				$plants[$plant->{'getName'.ucfirst(str_replace('_', '', $this->locale))}()] = $plant;
			}

			$dateFormat = $this->locale == 'hu' ? 'yyyy.MM.dd' : ($this->locale == 'sr_Latn' ? 'dd.MM.yyyy' : 'MM/dd/yyyy');

      $builder
        ->add('chemical', ChoiceType::class, [
					'label' => 'validators.reservations.chemical.label',
					'choices' => $chemicals,
					'placeholder' => 'validators.reservations.chemical.placeholder',
				])
				->add('plant', ChoiceType::class, [
					'label' => 'validators.reservations.plant.label',
					'choices' => $plants,
					'placeholder' => 'validators.reservations.plant.placeholder',
				])
				->add('parcel_number', NumberType::class, [
					'label' => 'validators.reservations.parcel_number.label',
					'attr' => [
						'placeholder' => ' ',
					],
				])
				->add('land_area', NumberType::class, [
					'label' => 'validators.reservations.land_area.label',
					'attr' => [
						'placeholder' => ' ',
					],
				])
				->add('to_be_present', ChoiceType::class, [
					'label' => 'validators.reservations.to_be_present.label',
					'placeholder' => 'validators.reservations.to_be_present.placeholder',
					'choices' => [
						'validators.reservations.to_be_present.yes' => 1,
						'validators.reservations.to_be_present.no' => 0,
					],
				])
				->add('comment', TextareaType::class, [
					'label' => 'validators.reservations.comment.label',
					'required' => false,
					'attr' => [
						'placeholder' => ' ',
					],
				])
				->add('reservation_interval_start', DateType::class, [
					'attr' => [
						'placeholder' => ' ',
					],
					'label' => 'validators.reservations.reservation_interval_start.label',
					'widget' => 'single_text',
					'html5' => false,
					'format' => $dateFormat,
					'constraints' => [
						new DateRange([
							'minDays' => 7,
							'dateFormat' => $dateFormat,
						]),
					],
				])
				->add('reservation_interval_end', DateType::class, [
					'attr' => [
						'placeholder' => ' ',
					],
					'label' => 'validators.reservations.reservation_interval_end.label',
					'widget' => 'single_text',
					'html5' => false,
					'format' => $dateFormat,
				])
				->add('gps_coordinates', CollectionType::class, [
					'required' => false,
				])
				->add('captcha', ReCaptchaType::class, [
					'type' => 'invisible', // (invisible, checkbox)
				])
      ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'translation_domain' => 'validators',
        ]);
    }
}
