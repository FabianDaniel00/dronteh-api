<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationFormType;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ReservationController extends AbstractController
{
    /**
     * @Route("/reservation", name="app_reservation")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        $currentUser = $this->getUser();

        if (!$currentUser) {
            return $this->redirectToRoute('app_home');
        }

        $reservation = new Reservation();
        $reservation->setUser($currentUser);
        $id = $request->query->get('id');

        if ($id) {
            $old_reservation = $entityManager->getRepository(Reservation::class)->findOneBy([
                'id' => $id,
                'user' => $currentUser->getId(),
            ]);

            if ($old_reservation) {
                $reservation->setChemical($old_reservation->getChemical());
                $reservation->setPlant($old_reservation->getPlant());
                $reservation->setParcelNumber($old_reservation->getParcelNumber());
                $reservation->setLandArea($old_reservation->getLandArea());
                $reservation->setToBePresent($old_reservation->getToBePresent());
                $reservation->setGpsCoordinates($old_reservation->getGpsCoordinates());
            }
        }

        $form = $this->createForm(ReservationFormType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $this->addFlash('danger', 'app.reservation.error');
            } else {
                $email = (new TemplatedEmail())
                    ->from(new Address($this->getParameter('sender_email'), $translator->trans('mails.reservation_notification.name', [], 'mails')))
                    ->to(new Address($currentUser->getEmail()))
                    ->subject($translator->trans('mails.reservation_notification.subject', ['%parcel_number%' => $reservation->getParcelNumber()], 'mails'))

                    // path of the Twig template to render
                    ->htmlTemplate('reservation/reservation_notification.html.twig')

                    // pass variables (name => value) to the template
                    ->context([
                        'name' => $currentUser->getFirstname().' '.$currentUser->getLastname(),
                        'reservation' => $reservation,
                        'locale' => $request->getLocale(),
                        'new_reservation' => true,
                        'time_interval_start' => $reservation->getReservationIntervalStart(),
                        'time_interval_end' => $reservation->getReservationIntervalEnd(),
                    ]);
                try {
                    $entityManager->persist($reservation);
                    $entityManager->flush();
                    $mailer->send($email);

                    $this->addFlash('success', 'app.reservation.success');

                    return $this->redirectToRoute('app_reservation');
                } catch(TransportExceptionInterface $e) {
                    $entityManager->remove($reservation);
                    $entityManager->flush();

                    $this->addFlash('danger', 'app.reservation.error');
                }
            }
        }

        return $this->render('app/reservation.html.twig', [
            'reservationForm' => $form->createView(),
        ]);
    }
}
