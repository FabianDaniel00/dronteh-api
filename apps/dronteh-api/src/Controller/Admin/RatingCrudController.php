<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Rating;
use App\Entity\Chemical;
use Doctrine\ORM\QueryBuilder;
use App\Controller\Admin\UserCrudController;
use App\Controller\Admin\ChemicalCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Admin\AbstractUndeleteCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;

class RatingCrudController extends AbstractUndeleteCrudController
{
    private AdminContextProvider $adminContextProvider;
    private TranslatorInterface $translator;

    public function __construct(AdminContextProvider $adminContextProvider, TranslatorInterface $translator)
    {
        $this->adminContextProvider = $adminContextProvider;
        $this->translator = $translator;
    }

    public static function getEntityFqcn(): string
    {
        return Rating::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return array_merge(parent::configureFields($pageName), [
            AssociationField::new('user', 'admin.singular.user')
                ->setCrudController(UserCrudController::class)
                ->autocomplete()
                ->setQueryBuilder(
                    fn (QueryBuilder $queryBuilder) => $queryBuilder
                        ->getEntityManager()
                        ->getRepository(User::class)
                        ->findBy([
                            'is_deleted' => false,
                        ], [
                            'firstname' => 'ASC',
                            'lastname' => 'ASC',
                        ])
                )
            ,
            AssociationField::new('chemical', 'admin.singular.chemical')
                ->setCrudController(ChemicalCrudController::class)
                ->autocomplete()
                ->setQueryBuilder(
                    fn (QueryBuilder $queryBuilder) => $queryBuilder
                        ->getEntityManager()
                        ->getRepository(Chemical::class)
                        ->findBy([
                            'is_deleted' => false,
                        ], [
                            'name_'.$this->adminContextProvider->getContext()->getRequest()->getLocale() => 'ASC',
                        ])
                )
            ,
            NumberField::new('rating', 'admin.singular.rating'),
        ]);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('admin.singular.rating')
            ->setEntityLabelInPlural('admin.plural.rating')
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return parent::configureFilters($filters)
            ->add(NumericFilter::new('rating', $this->translator->trans('admin.singular.rating', [], 'admin')))
            ->add(EntityFilter::new('user', $this->translator->trans('admin.singular.user', [], 'admin')))
            ->add(EntityFilter::new('chemical', $this->translator->trans('admin.singular.chemical', [], 'admin')))
        ;
    }
}
