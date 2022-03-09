<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    private AdminUrlGenerator $routeBuilder;

    public function __construct(AdminUrlGenerator $routeBuilder)
    {
        $this->routeBuilder = $routeBuilder;
    }

    /**
     * @Route("/login", name="admin_login", methods={"GET", "POST"})
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() && in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
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
        return $this->redirect($this->routeBuilder->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('DronTeh Admin')

            ->setTranslationDomain('admin')

            ->renderContentMaximized()
        ;
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::section('admin.menu.section.users'),
            MenuItem::linkToCrud('admin.menu.items.users', 'fas fa-users', User::class),

            MenuItem::section(),
            MenuItem::section(),
            MenuItem::section('admin.menu.section.logout'),
            MenuItem::linkToLogout('admin.logout', 'fas fa-sign-out-alt'),
        ];
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setGravatarEmail($user->getEmail())
        ;
    }
}
