<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ArrayFilter;
use App\Controller\Admin\AbstractUndeleteCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractUndeleteCrudController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $supportedLocales = explode('|', $this->getParameter('app.supported_locales'));
        $locales = [];
        foreach ($supportedLocales as $supportedLocale) {
            $locales['admin.locales.'.$supportedLocale] = $supportedLocale;
        }

        $rolesField = ArrayField::new('roles', 'admin.list.users.role')
            ->setTemplatePath('@EasyAdmin/crud/field/roles-array.html.twig')
            ->setPermission('ROLE_SUPER_ADMIN')
        ;
        $createdAt = DateTimeField::new('created_at', 'admin.list.created_at');
        $updatedAt = DateTimeField::new('updated_at', 'admin.list.updated_at');

        if ($pageName === Crud::PAGE_EDIT) {
            $createdAt->addCssClass('d-none');
            $updatedAt->addCssClass('d-none');
        } else {
            $createdAt->hideWhenCreating();
            $updatedAt->hideWhenCreating();
        }

        if (!in_array('ROLE_SUPER_ADMIN', $this->getUser()->getRoles())) $rolesField->hideOnForm();

        if ($pageName !== Crud::PAGE_DETAIL) $rolesField->setHelp('admin.list.users.help.role');

        return array_merge(parent::configureFields($pageName), [
            EmailField::new('email', 'admin.list.users.email'),
            TextField::new('password', 'admin.list.users.password')->setFormType(PasswordType::class)->onlyWhenCreating(),
            HiddenField::new('password', 'admin.list.users.password')->onlyWhenUpdating(),
            TextField::new('firstname', 'admin.list.users.firstname')->setColumns(4),
            TextField::new('lastname', 'admin.list.users.lastname')->setColumns(4),
            FormField::addRow(),
            TelephoneField::new('tel', 'admin.list.users.tel'),
            $rolesField,
            ChoiceField::new('locale', 'admin.list.users.locale')->setChoices($locales),
            $createdAt,
            $updatedAt,
            BooleanField::new('isVerified', 'admin.list.users.is_verified')->renderAsSwitch(false)->hideOnForm(),
        ]);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('admin.singular.user')
            ->setEntityLabelInPlural('admin.plural.user')

            ->setHelp(Crud::PAGE_INDEX, $this->translator->trans('admin.index.users.help', [], 'admin'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $newPassword = Action::new('newPassword', 'admin.buttons.new_password', 'fas fa-lock')
            ->linkToRoute('app_forgot_password_request')
            ->setHtmlAttributes(['target' => '_blank'])
            ->displayIf(function ($entity) {
                return $entity->getId() === $this->getUser()->getId();
            })
        ;

        return $actions
            ->add(Crud::PAGE_EDIT, $newPassword)

            ->reorder(Crud::PAGE_EDIT, [
                Action::INDEX,
                Action::NEW,
                'newPassword',
                Action::DELETE,
                'undelete',
                Action::DETAIL,
                Action::SAVE_AND_CONTINUE,
                'saveAndViewDetail',
                Action::SAVE_AND_RETURN,
            ])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        $supportedRoles = $this->getParameter('app.supported_roles');
        $locales = [];
        foreach ($supportedRoles as $supportedRole) {
            $locales[$this->translator->trans('admin.list.users.roles.'.strtolower(explode('ROLE_', $supportedRole)[1]), [], 'admin')] = $supportedRole;
        }

        return parent::configureFilters($filters)
            ->add(BooleanFilter::new('isVerified', $this->translator->trans('admin.list.users.is_verified', [], 'admin')))
            ->add(ArrayFilter::new('roles', $this->translator->trans('admin.list.users.role', [], 'admin'))
                ->setChoices($locales)
                ->canSelectMultiple()
            )
            ->add(DateTimeFilter::new('created_at', $this->translator->trans('admin.list.created_at', [], 'admin')))
            ->add(DateTimeFilter::new('updated_at', $this->translator->trans('admin.list.updated_at', [], 'admin')))
        ;
    }
}
