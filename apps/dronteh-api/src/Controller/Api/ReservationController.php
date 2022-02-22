<?php

namespace App\Controller\Api;

use App\Entity\Reservation;
use Symfony\Component\Mime\Address;
use App\Repository\ReservationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\JsonApi\Document\Reservation\ReservationDocument;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\JsonApi\Document\Reservation\ReservationsDocument;
use App\JsonApi\Transformer\ReservationResourceTransformer;
use App\JsonApi\Hydrator\Reservation\CreateReservationHydrator;
use App\JsonApi\Hydrator\Reservation\UpdateReservationHydrator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @Route("/reservations")
 */
class ReservationController extends Controller
{
    /**
     * @Route("/", name="reservations_index", methods="GET")
     */
    public function index(ReservationRepository $reservationRepository, ResourceCollection $resourceCollection, Request $request): Response
    {
        $resourceCollection->setRepository($reservationRepository);

        $resourceCollection->getQuery()->where('r.is_deleted = 0');
        $resourceCollection->handleIndexRequest();

        return $this->respondOk(
            new ReservationsDocument(new ReservationResourceTransformer($request->getLocale())),
            $resourceCollection
        );
    }

    /**
     * @Route("/", name="reservations_new", methods="POST")
     */
    public function new(Request $request, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            throw new AccessDeniedHttpException('api.current_user.null');
        }

        $reservation = $this->jsonApi()->hydrate(
            new CreateReservationHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            new Reservation()
        );

        $this->validate($reservation);

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

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
                'time_interval_start' => date('Y-m-d', strtotime($reservation->getReservationIntervalStart()->format(\DATE_ATOM))),
                'time_interval_end' => date('Y-m-d', strtotime($reservation->getReservationIntervalEnd()->format(\DATE_ATOM))),
            ]);

        try {
            $mailer->send($email);
        } catch(TransportExceptionInterface $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'api.reservations.notification.email_error');
        }

        return $this->respondOk(
            new ReservationDocument(new ReservationResourceTransformer($request->getLocale())),
            $reservation
        );
    }

    /**
     * @Route("/{id}", name="reservations_show", methods="GET")
     */
    public function show(Reservation $reservation, Request $request): Response
    {
        if ($reservation->isDeleted()) {
            throw $this->createNotFoundException('api.reservations.is_deleted');
        }

        return $this->respondOk(
            new ReservationDocument(new ReservationResourceTransformer($request->getLocale())),
            $reservation
        );
    }

    /**
     * @Route("/{id}", name="reservations_edit", methods="PATCH")
     */
    public function edit(Reservation $reservation, Request $request): Response
    {
        if ($reservation->isDeleted()) {
            throw $this->createNotFoundException('api.reservations.is_deleted');
        }

        if ($reservation->getStatus() !== 0) {
            throw $this->createNotFoundException('api.reservations.edit.status_0');
        }

        $reservation = $this->jsonApi()->hydrate(
            new UpdateReservationHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            $reservation
        );

        $this->validate($reservation);

        $this->entityManager->flush();

        return $this->respondOk(
            new ReservationDocument(new ReservationResourceTransformer($request->getLocale())),
            $reservation
        );
    }

    /**
     * @Route("/{id}", name="reservations_delete", methods="DELETE")
     */
    public function delete(Reservation $reservation): Response
    {
        if ($reservation->isDeleted()) {
            throw $this->createNotFoundException('api.reservations.is_deleted');
        }

        $reservation->setIsDeleted(1);
        $this->entityManager->flush();

        return $this->respondNoContent();
    }

    /**
     * @Route("/send_notification", name="send_reservation_notification", methods="POST")
     */
    public function sendNotification(Request $request, ReservationRepository $reservationRepository, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        $requestData = json_decode($request->getContent(), true);
        $requestData = $requestData['data'];

        $id = $requestData['reservation_id'];
        if($id === null) {
            throw new BadRequestHttpException('api.reservations.notification.id_null');
        }

        $time = $requestData['time'];
        if($time === null || $time === '') {
            throw new BadRequestHttpException('api.reservations.notification.message_null');
        }

        $reservation = $reservationRepository->find($id);
        if($reservation === null) {
            return $this->createNotFoundException('api.reservations.notification.reservation_null');
        }

        $reservation->setTime($time);

        $this->validate($reservation);

        $this->entityManager->flush();

        $userReservation = $reservation->getUser();

        $locale = $userReservation->getLocale();

        $email = (new TemplatedEmail())
            ->from(new Address($this->getParameter('sender_email'), $translator->trans('mails.reservation_notification.name', [], 'mails', $locale)))
            ->to(new Address($userReservation->getEmail()))
            ->subject($translator->trans('mails.reservation_notification.subject', ['%parcel_number%' => $reservation->getParcelNumber()], 'mails', $locale))

            // path of the Twig template to render
            ->htmlTemplate('reservation/reservation_notification.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'name' => $userReservation->getFirstname().' '.$userReservation->getLastname(),
                'time' => date('Y-m-d H:i:s', strtotime($time)),
                'reservation' => $reservation,
                'locale' => $locale,
                'new_reservation' => false,
            ]);

        try {
            $mailer->send($email);
        } catch(TransportExceptionInterface $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'api.reservations.notification.email_error');
        }

        return $this->respondNoContent();
    }
}