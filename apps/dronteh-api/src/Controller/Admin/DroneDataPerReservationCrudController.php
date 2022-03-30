<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use Doctrine\ORM\QueryBuilder;
use App\Entity\DroneDataPerReservation;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Controller\Admin\ReservationCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Admin\AbstractUndeleteCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class DroneDataPerReservationCrudController extends AbstractUndeleteCrudController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getEntityFqcn(): string
    {
        return DroneDataPerReservation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $createdAt = DateTimeField::new('created_at', 'admin.list.created_at');

        if ($pageName !== Crud::PAGE_EDIT) {
            $createdAt->hideWhenCreating();
        } else {
            $createdAt->addCssClass('d-none');
        }

        return array_merge(parent::configureFields($pageName), [
            AssociationField::new('reservation', 'admin.singular.reservation')
                ->setCrudController(ReservationCrudController::class)
                ->autocomplete()
                ->setQueryBuilder(
                    fn (QueryBuilder $queryBuilder) => $queryBuilder
                        ->getEntityManager()
                        ->getRepository(Reservation::class)
                        ->findBy([
                            'is_deleted' => false,
                        ], [
                            'reservation_interval_start' => 'DESC',
                            'reservation_interval_end' => 'DESC',
                        ])
                )
            ,
            TextareaField::new('results', 'admin.list.drone_data_per_reservations.results')->setMaxLength(5000)->setNumOfRows(6)->stripTags()->hideOnIndex(),
            NumberField::new('chemical_quantity_per_ha', 'admin.list.drone_data_per_reservations.chemical_quantity_per_ha'),
            NumberField::new('water_quantity', 'admin.list.drone_data_per_reservations.water_quantity'),
            $createdAt,
        ]);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('admin.singular.drone_data_per_reservation')
            ->setEntityLabelInPlural('admin.plural.drone_data_per_reservation')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add(EntityFilter::new('reservation', $this->translator->trans('admin.singular.reservation', [], 'admin')))
            ->add(NumericFilter::new('chemical_quantity_per_ha', $this->translator->trans('admin.list.drone_data_per_reservations.chemical_quantity_per_ha', [], 'admin')))
            ->add(NumericFilter::new('water_quantity', $this->translator->trans('admin.list.drone_data_per_reservations.water_quantity', [], 'admin')))
        ;
    }
}
