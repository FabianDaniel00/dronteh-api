<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private AdminUrlGenerator $adminUrlGenerator;
    private TranslatorInterface $translator;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, AdminUrlGenerator $adminUrlGenerator, TranslatorInterface $translator)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->translator = $translator;
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
            $locales['admin.list.users.locales.'.$supportedLocale] = $supportedLocale;
        }

        return [
            IdField::new('id')->setLabel('admin.list.users.id')->hideOnForm(),
            EmailField::new('email')->setLabel('admin.list.users.email'),
            TextField::new('password')->setFormType(PasswordType::class)->setLabel('admin.list.users.password')->onlyWhenCreating(),
            HiddenField::new('password')->setLabel('admin.list.users.password')->onlyWhenUpdating(),
            TextField::new('firstname')->setLabel('admin.list.users.firstname'),
            TextField::new('lastname')->setLabel('admin.list.users.lastname'),
            TelephoneField::new('tel')->setLabel('admin.list.users.tel'),
            ArrayField::new('roles')->setLabel('admin.list.users.role')->setHelp('admin.list.users.help.role'),
            ChoiceField::new('locale')->setLabel('admin.list.users.locale')->setChoices($locales),
            FormField::addRow(),
            DateTimeField::new('created_at')->setLabel('admin.list.users.created_at')->hideOnForm(),
            DateTimeField::new('updated_at')->setLabel('admin.list.users.updated_at')->hideOnForm(),
            BooleanField::new('isVerified')->setLabel('admin.list.users.is_verified')->renderAsSwitch(false)->hideOnForm(),
            BooleanField::new('is_deleted')->setLabel('admin.list.users.is_deleted')->setHelp('admin.list.users.help.is_deleted')->hideWhenCreating(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('admin.singular.users')
            ->setEntityLabelInPlural('admin.plural.users')

            ->setPageTitle(Crud::PAGE_INDEX, 'admin.list.users.title')
            ->setPageTitle(Crud::PAGE_DETAIL, 'admin.title.detail')
            ->setPageTitle(Crud::PAGE_EDIT, 'admin.title.edit')

            ->setHelp(Crud::PAGE_INDEX, $this->translator->trans('admin.index.users.help', [], 'admin'))
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $saveAndViewDetail = Action::new('saveAndViewDetail', null, 'fas fa-eye')
            ->displayAsButton()
            ->setHtmlAttributes(['name' => 'ea[newForm][btn]', 'value' => 'saveAndViewDetail'])
            // removes all existing CSS classes of the action and sets
            // the given value as the CSS class of the HTML element
            // ->setCssClass('btn btn-primary action-foo')
            // adds the given value to the existing CSS classes of the action (this is
            // useful when customizing a built-in action, which already has CSS classes)
            // ->addCssClass('some-custom-css-class text-danger')
            ->linkToCrudAction(Action::SAVE_AND_CONTINUE)
        ;

        return $actions
            // add actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)

            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, $saveAndViewDetail)

            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add(Crud::PAGE_NEW, $saveAndViewDetail)

            //reorder actions
            ->reorder(Crud::PAGE_NEW, [Action::INDEX, Action::SAVE_AND_ADD_ANOTHER, 'saveAndViewDetail', Action::SAVE_AND_RETURN])
            ->reorder(Crud::PAGE_DETAIL, [Action::INDEX, Action::EDIT, Action::DELETE])
            ->reorder(Crud::PAGE_EDIT, [Action::INDEX, Action::DETAIL, Action::SAVE_AND_CONTINUE, 'saveAndViewDetail', Action::SAVE_AND_RETURN])

            //update actions
            ->update(Crud::PAGE_NEW, 'saveAndViewDetail',
                fn (Action $action) => $action->setLabel('admin.new.save_and_view_detail'))
            ->update(Crud::PAGE_NEW, Action::INDEX,
                fn (Action $action) => $action->setIcon('fas fa-angle-left'))
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN,
                fn (Action $action) => $action->setIcon('fas fa-plus'))
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER,
                fn (Action $action) => $action->setIcon('fas fa-plus-square'))

            ->update(Crud::PAGE_INDEX, Action::NEW,
                fn (Action $action) => $action->setIcon('fas fa-plus'))
            ->update(Crud::PAGE_INDEX, Action::DETAIL,
                fn (Action $action) => $action->setIcon('fas fa-eye'))
            ->update(Crud::PAGE_INDEX, Action::EDIT,
                fn (Action $action) => $action->setIcon('far fa-edit'))
            ->update(Crud::PAGE_INDEX, Action::DELETE,
                fn (Action $action) => $action->setIcon('fa fa-trash-o text-danger'))

            ->update(Crud::PAGE_DETAIL, Action::INDEX,
                fn (Action $action) => $action->setIcon('fas fa-angle-left'))
            ->update(Crud::PAGE_DETAIL, Action::EDIT,
                fn (Action $action) => $action->setIcon('far fa-edit'))

            ->update(Crud::PAGE_EDIT, 'saveAndViewDetail',
                fn (Action $action) => $action->setLabel('admin.edit.save_and_view_detail'))
            ->update(Crud::PAGE_EDIT, Action::INDEX,
                fn (Action $action) => $action->setIcon('fas fa-angle-left'))
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE,
                fn (Action $action) => $action->setLabel('admin.edit.save_and_continue'))
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN,
                fn (Action $action) => $action->setIcon('fas fa-save'))
            ->update(Crud::PAGE_EDIT, Action::DETAIL,
                fn (Action $action) => $action->setIcon('fas fa-eye'))
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('is_deleted')->setLabel($this->translator->trans('admin.list.users.is_deleted', [], 'admin')))
            ->add(BooleanFilter::new('isVerified')->setLabel($this->translator->trans('admin.list.users.is_verified', [], 'admin')))
            ->add(DateTimeFilter::new('created_at')->setLabel($this->translator->trans('admin.list.users.created_at', [], 'admin')))
            ->add(DateTimeFilter::new('updated_at')->setLabel($this->translator->trans('admin.list.users.updated_at', [], 'admin')))
            ->add(ArrayFilter::new('roles')->setLabel($this->translator->trans('admin.list.users.role', [], 'admin')))
        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setPassword(
            $this->userPasswordHasher->hashPassword(
                $entityInstance,
                $entityInstance->getPassword()
            )
        );

        $entityManager->persist($entityInstance);
        $entityManager->flush();
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
}
