<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ArrayFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $adminUrlGenerator;
    private TranslatorInterface $translator;
    private ManagerRegistry $doctrine;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, TranslatorInterface $translator, ManagerRegistry $doctrine)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->translator = $translator;
        $this->doctrine = $doctrine;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $supportedLocales = explode('|', $this->getParameter('app.supported_locales'));
        $locales = [null => $this->getParameter('app.default_locale')];
        foreach ($supportedLocales as $supportedLocale) {
            $locales['admin.locales.'.$supportedLocale] = $supportedLocale;
        }

        return [
            IdField::new('id')->setLabel('admin.list.users.id')->hideOnForm(),
            EmailField::new('email')->setLabel('admin.list.users.email'),
            TextField::new('password')->setFormType(PasswordType::class)->setLabel('admin.list.users.password')->onlyWhenCreating(),
            HiddenField::new('password')->setLabel('admin.list.users.password')->onlyWhenUpdating(),
            TextField::new('firstname')->setLabel('admin.list.users.firstname')->setColumns(3),
            TextField::new('lastname')->setLabel('admin.list.users.lastname')->setColumns(3),
            FormField::addRow(),
            TelephoneField::new('tel')->setLabel('admin.list.users.tel'),
            ArrayField::new('roles')
                ->setTemplatePath('bundles/EasyAdminBundle/crud/field/array.html.twig')
                ->setLabel('admin.list.users.role')
                ->setHelp('admin.list.users.help.role'),
            ChoiceField::new('locale')->setLabel('admin.list.users.locale')->setChoices($locales),
            DateTimeField::new('created_at')->setLabel('admin.list.users.created_at')->hideOnForm(),
            DateTimeField::new('updated_at')->setLabel('admin.list.users.updated_at')->hideOnForm(),
            BooleanField::new('isVerified')->setLabel('admin.list.users.is_verified')->renderAsSwitch(false)->hideOnForm(),
            BooleanField::new('is_deleted')->setLabel('admin.list.users.is_deleted')->setHelp('admin.list.users.help.is_deleted')->hideOnForm(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('admin.singular.user')
            ->setEntityLabelInPlural('admin.plural.user')

            ->setPageTitle(Crud::PAGE_INDEX, 'admin.title.index')
            ->setPageTitle(Crud::PAGE_DETAIL, 'admin.title.detail')
            ->setPageTitle(Crud::PAGE_EDIT, 'admin.title.edit')

            ->setHelp(Crud::PAGE_INDEX, $this->translator->trans('admin.index.users.help', [], 'admin'))
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $supportedRoles = $this->getParameter('app.supported_roles');
        $locales = [];
        foreach ($supportedRoles as $supportedRole) {
            $locales[$this->translator->trans('admin.list.users.roles.'.strtolower(explode('ROLE_', $supportedRole)[1]), [], 'admin')] = $supportedRole;
        }

        return $filters
            ->add(BooleanFilter::new('is_deleted')->setLabel($this->translator->trans('admin.list.users.is_deleted', [], 'admin')))
            ->add(BooleanFilter::new('isVerified')->setLabel($this->translator->trans('admin.list.users.is_verified', [], 'admin')))
            ->add(DateTimeFilter::new('created_at')->setLabel($this->translator->trans('admin.list.users.created_at', [], 'admin')))
            ->add(DateTimeFilter::new('updated_at')->setLabel($this->translator->trans('admin.list.users.updated_at', [], 'admin')))
            ->add(ArrayFilter::new('roles')
                ->setLabel($this->translator->trans('admin.list.users.role', [], 'admin'))
                ->setChoices($locales)
            )
        ;
    }

    protected function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        $submitButtonName = $context->getRequest()->request->all()['ea']['newForm']['btn'];

        if ('saveAndViewDetail' === $submitButtonName) {
            $url = $this->adminUrlGenerator
                ->setAction(Action::DETAIL)
                ->setEntityId($context->getEntity()->getPrimaryKeyValue())
                ->generateUrl();

            return $this->redirect($url);
        }

        return parent::getRedirectResponseAfterSave($context, $action);
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setIsDeleted(1);
        $entityManager->flush();
    }

    public function undelete(AdminContext $context): RedirectResponse
    {
        $entity = $context->getEntity()->getInstance();

        $entity->setIsDeleted(0);
        $this->doctrine->getManager()->flush();

        return $this->redirect($this->adminUrlGenerator
            ->setAction(Action::EDIT)
            ->generateUrl()
        );
    }

    public function batchUndelete(BatchActionDto $batchActionDto): RedirectResponse
    {
        $entityFqcn = $batchActionDto->getEntityFqcn();

        $entityManager = $this->doctrine->getManagerForClass($batchActionDto->getEntityFqcn());
        foreach ($batchActionDto->getEntityIds() as $id) {
            $entity = $entityManager->find($entityFqcn, $id);
            $entity->setIsDeleted(0);
        }

        $entityManager->flush();

        return $this->redirect($batchActionDto->getReferrerUrl());
    }
}
