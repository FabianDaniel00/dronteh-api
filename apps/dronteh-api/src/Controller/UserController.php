<?php

namespace App\Controller;

use App\Form\EditUserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="app_user")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $currentUser = $this->getUser();

        if (!$currentUser) {
            return $this->redirectToRoute('app_home');
        }

        $user = $currentUser;
        $form = $this->createForm(EditUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('danger', 'app.user_edit.error');
            } else {
                $entityManager->flush();

                $this->addFlash('success', 'app.user_edit.success_edit');

                return $this->redirectToRoute('app_user');
            }
        }

        $dql = 'SELECT r FROM App\Entity\Reservation r';
        $query = $entityManager->createQuery($dql);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10, /*limit per page*/
            [
                'defaultSortFieldName' => 'r.time',
                'defaultSortDirection' => 'asc',
            ]
        );

        return $this->render('app/user.html.twig', [
            'editUserForm' => $form->createView(),
            'pagination' => $pagination,
        ]);
    }
}
