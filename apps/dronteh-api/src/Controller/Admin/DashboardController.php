<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Reservation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    /**
     * @Route("/login", name="admin_login", methods={"GET", "POST"})
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $currentUser = $this->getUser();
        if ($currentUser && count(array_intersect(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], $currentUser->getRoles()))) {
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('@EasyAdmin/page/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),

            'csrf_token_intention' => 'authenticate',

            'target_path' => $this->generateUrl('admin_dashboard'),

            'username_label' => 'admin.login.form.email',

            'username_parameter' => 'email',
            'password_parameter' => 'password',

            'forgot_password_enabled' => true,
            'forgot_password_path' => $this->generateUrl('app_forgot_password_request'),

            'remember_me_enabled' => true,
            'remember_me_checked' => true,
        ]);
    }

    /**
     * @Route("/logout", name="admin_logout", methods="GET")
     */
    public function logout(): void {}

    /**
     * @Route("/", name="admin_dashboard")
     */
    public function index(): Response
    {
        return $this->redirect($this->adminUrlGenerator->setController(UserCrudController::class)
            ->set('filters[is_deleted][comparison]', '0')
            ->generateUrl()
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="../../assets/images/dronteh_logo.png" alt="dronteh_logo.png" width="80em"><b>DronTeh Admin</b>')

            ->setTranslationDomain('admin')

            ->renderContentMaximized()

            ->setFaviconPath('public/build/images/favicon.png')
        ;
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->setPageTitle(Crud::PAGE_INDEX, 'admin.title.index')
            ->setPageTitle(Crud::PAGE_DETAIL, 'admin.title.detail')
            ->setPageTitle(Crud::PAGE_EDIT, 'admin.title.edit')
        ;
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::section('admin.plural.user'),
            MenuItem::linkToCrud('admin.plural.user', 'fas fa-users', User::class)
                ->setQueryParameter('filters[is_deleted][comparison]', '0')
            ,

            MenuItem::section('admin.plural.reservation'),
            MenuItem::linkToCrud('admin.plural.reservation', 'fa fa-ticket', Reservation::class)
                ->setQueryParameter('filters[is_deleted][comparison]', '0')
            ,

            MenuItem::section(),
            MenuItem::section(),
            MenuItem::section('__ea__user.sign_out'),
            MenuItem::linkToLogout('__ea__user.sign_out', 'fas fa-sign-out-alt'),
        ];
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        $urlToProfile = $this->adminUrlGenerator
            ->setController(UserCrudController::class)
            ->setAction(Action::EDIT)
            ->setEntityId($user->getId())
        ;

        return parent::configureUserMenu($user)
            ->setGravatarEmail($user->getEmail())

            ->addMenuItems([
                MenuItem::linkToUrl('admin.buttons.my_profile', 'fas fa-user-circle', $urlToProfile),
            ]);
        ;
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addWebpackEncoreEntry('admin')
        ;
    }

    public function configureActions(): Actions
    {
        $saveAndViewDetail = Action::new('saveAndViewDetail', 'admin.buttons.save_and_view_detail', 'fas fa-eye')
            ->displayAsButton()
            ->setCssClass('action-saveAndViewDetail btn btn-success')
            ->setHtmlAttributes([
                'name' => 'ea[newForm][btn]',
                'value' => 'saveAndViewDetail',
            ])
            ->linkToCrudAction(Action::SAVE_AND_RETURN)
        ;

        $undelete = Action::new('undelete', 'admin.buttons.undelete', 'fas fa-undo text-warning')
            ->setHtmlAttributes([
                'name' => 'ea[newForm][btn]',
                'value' => 'undelete',
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-undelete',
            ])
            ->addCssClass('action-undelete text-warning')
            ->linkToCrudAction('undelete')
            ->displayIf(static function ($entity) {
                return method_exists($entity, 'setIsDeleted') && $entity->isDeleted();
            })
        ;

        return Actions::new()
            // add actions
            ->addBatchAction(Action::BATCH_DELETE)
            ->addBatchAction(Action::new('batchUndelete', 'admin.buttons.undelete', 'fas fa-undo')
                ->linkToCrudAction('batchUndelete')
                ->addCssClass('btn text-info')
            )

            ->add(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, Action::EDIT)
            ->add(Crud::PAGE_INDEX, $undelete)
            ->add(Crud::PAGE_INDEX, Action::DELETE)

            ->add(Crud::PAGE_DETAIL, Action::NEW)
            ->add(Crud::PAGE_DETAIL, Action::INDEX)
            ->add(Crud::PAGE_DETAIL, Action::EDIT)
            ->add(Crud::PAGE_DETAIL, Action::DELETE)
            ->add(Crud::PAGE_DETAIL, $undelete)

            ->add(Crud::PAGE_EDIT, Action::NEW)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_EDIT, Action::DELETE)
            ->add(Crud::PAGE_EDIT, $undelete)
            ->add(Crud::PAGE_EDIT, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->add(Crud::PAGE_EDIT, $saveAndViewDetail)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN)

            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER)
            ->add(Crud::PAGE_NEW, $saveAndViewDetail)
            ->add(Crud::PAGE_NEW, Action::SAVE_AND_RETURN)

            //reorder actions
            ->reorder(Crud::PAGE_INDEX, [Action::EDIT, Action::DETAIL, 'undelete', 'delete'])
            ->reorder(Crud::PAGE_NEW, [Action::INDEX, Action::SAVE_AND_ADD_ANOTHER, 'saveAndViewDetail', Action::SAVE_AND_RETURN])
            ->reorder(Crud::PAGE_DETAIL, [Action::INDEX, Action::NEW, Action::EDIT, Action::DELETE, 'undelete'])
            ->reorder(Crud::PAGE_EDIT, [
                Action::INDEX,
                Action::NEW,
                Action::DELETE,
                'undelete',
                Action::DETAIL,
                Action::SAVE_AND_CONTINUE,
                'saveAndViewDetail',
                Action::SAVE_AND_RETURN,
            ])

            //update actions
            ->update(Crud::PAGE_NEW, Action::INDEX,
                fn (Action $action) => $action->setIcon('fas fa-angle-left'))
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN,
                fn (Action $action) => $action->setIcon('fas fa-plus'))
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER,
                fn (Action $action) => $action->setIcon('fas fa-plus-square')->setCssClass('action-saveAndAddAnother btn btn-warning'))

            ->update(Crud::PAGE_INDEX, Action::BATCH_DELETE,
                fn (Action $action) => $action->setIcon('fa fa-trash-o'))
            ->update(Crud::PAGE_INDEX, Action::NEW,
                fn (Action $action) => $action->setIcon('fas fa-plus'))
            ->update(Crud::PAGE_INDEX, Action::DETAIL,
                fn (Action $action) => $action->setIcon('fas fa-eye'))
            ->update(Crud::PAGE_INDEX, Action::EDIT,
                fn (Action $action) => $action->setIcon('far fa-edit'))
            ->update(Crud::PAGE_INDEX, Action::DELETE,
                fn (Action $action) => $action
                    ->setIcon('fa fa-trash-o text-danger')
                    ->displayIf(static function ($entity) {
                        return !method_exists($entity, 'setIsDeleted') || !$entity->isDeleted();
                    })
            )

            ->update(Crud::PAGE_DETAIL, Action::INDEX,
                fn (Action $action) => $action->setIcon('fas fa-angle-left'))
            ->update(Crud::PAGE_DETAIL, Action::EDIT,
                fn (Action $action) => $action->setIcon('far fa-edit'))
            ->update(Crud::PAGE_DETAIL, Action::DELETE,
                fn (Action $action) => $action->displayIf(static function ($entity) {
                    return !method_exists($entity, 'setIsDeleted') || !$entity->isDeleted();
                }))
            ->update(Crud::PAGE_DETAIL, 'undelete',
                fn (Action $action) => $action->addCssClass('btn'))

            ->update(Crud::PAGE_EDIT, Action::INDEX,
                fn (Action $action) => $action->setIcon('fas fa-angle-left'))
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE,
                fn (Action $action) => $action->setLabel('admin.buttons.save_and_continue'))
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN,
                fn (Action $action) => $action->setIcon('fas fa-save'))
            ->update(Crud::PAGE_EDIT, Action::DETAIL,
                fn (Action $action) => $action->setIcon('fas fa-eye')->setCssClass('action-detail btn btn-info'))
            ->update(Crud::PAGE_EDIT, Action::DELETE,
                fn (Action $action) => $action->displayIf(static function ($entity) {
                    return !method_exists($entity, 'setIsDeleted') || !$entity->isDeleted();
                })
            )
            ->update(Crud::PAGE_EDIT, 'undelete',
                fn (Action $action) => $action->addCssClass('btn'))
        ;
    }
}
