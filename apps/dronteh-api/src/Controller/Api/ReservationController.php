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
    public function index(ReservationRepository $reservationRepository, ResourceCollection $resourceCollection, Request $request, TranslatorInterface $translator): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            throw new AccessDeniedHttpException($translator->trans('api.current_user.null', [], 'api'));
        }

        $resourceCollection->setRepository($reservationRepository);

        $resourceCollection
            ->getQuery()
            ->where('r.is_deleted = 0')
            ->andWhere('r.user = :user_id')
            ->setParameter('user_id', $currentUser->getId());
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
            throw new AccessDeniedHttpException($translator->trans('api.current_user.null', [], 'api'));
        }

        $reservation = $this->jsonApi()->hydrate(
            new CreateReservationHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            new Reservation()
        );

        $differenceBetweenInterval = 7; //days
        if ($reservation->getReservationIntervalStart()->modify('+'.$differenceBetweenInterval.' days') > $reservation->getReservationIntervalEnd()) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, $translator->trans('api.reservations.edit.less_difference', ['%day%' => $differenceBetweenInterval], 'api'));
        }

        $reservation->setUser($currentUser);

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
                'time_interval_start' => $reservation->getReservationIntervalStart(),
                'time_interval_end' => $reservation->getReservationIntervalEnd(),
            ]);

        try {
            $mailer->send($email);
        } catch(TransportExceptionInterface $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $translator->trans('api.reservations.notification.email_error', [], 'api'));
        }

        return $this->respondOk(
            new ReservationDocument(new ReservationResourceTransformer($request->getLocale())),
            $reservation
        );
    }

    /**
     * @Route("/{id}", name="reservations_show", methods="GET")
     */
    public function show(Reservation $reservation, Request $request, TranslatorInterface $translator): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            throw new AccessDeniedHttpException($translator->trans('api.current_user.null', [], 'api'));
        }

        if (!$reservation || $reservation->isDeleted() || $reservation->getUser()->getId() !== $currentUser->getId()) {
            throw $this->createNotFoundException($translator->trans('api.reservations.not_found', [], 'api'));
        }

        return $this->respondOk(
            new ReservationDocument(new ReservationResourceTransformer($request->getLocale())),
            $reservation
        );
    }

    /**
     * @Route("/{id}", name="reservations_edit", methods="PATCH")
     */
    public function edit(Reservation $reservation, Request $request, TranslatorInterface $translator): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            throw new AccessDeniedHttpException($translator->trans('api.current_user.null', [], 'api'));
        }

        if ($reservation->isDeleted() || $reservation->getUser()->getId() !== $currentUser->getId()) {
            throw $this->createNotFoundException($translator->trans('api.reservations.not_found', [], 'api'));
        }

        if ($reservation->getStatus() !== 1) { //active
            throw $this->createNotFoundException($translator->trans('api.reservations.edit.status', [], 'api'));
        }

        $valueBeforeAction = 3; // days
        if ($reservation->getTime() && $reservation->getTime()->modify('-'.$valueBeforeAction.' days') < new \DateTime('@'.strtotime('now')) ) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, $translator->trans('api.reservations.edit.time', ['%day%' => $valueBeforeAction], 'api'));
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
    public function delete(Reservation $reservation, TranslatorInterface $translator): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            throw new AccessDeniedHttpException($translator->trans('api.current_user.null', [], 'api'));
        }

        if ($reservation->isDeleted() || $reservation->getUser()->getId() !== $currentUser->getId()) {
            throw $this->createNotFoundException($translator->trans('api.reservations.not_found', [], 'api'));
        }

        $reservation->setIsDeleted(1);
        $this->entityManager->flush();

        return $this->respondNoContent();
    }

    /**
     * @Route("/{id}/send_notification", name="send_reservation_notification", methods="POST", requirements={"id"="\d+"})
     */
    public function sendNotification(Request $request, ReservationRepository $reservationRepository, MailerInterface $mailer, TranslatorInterface $translator, int $id): Response
    {
        $requestData = json_decode($request->getContent(), true);
        $requestData = $requestData['data'];

        $time = new \DateTime($requestData['time']);
        if($time === null || $time === '') {
            throw new BadRequestHttpException($translator->trans('api.reservations.notification.time_null', [], 'api'));
        }

        $reservation = $reservationRepository->find($id);
        if($reservation === null) {
            throw $this->createNotFoundException($translator->trans('api.reservations.not_found', [], 'api'));
        }

        $reservation->setTime($time);

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
                'time' => $time,
                'reservation' => $reservation,
                'locale' => $locale,
                'new_reservation' => false,
            ]);

        try {
            $mailer->send($email);
        } catch(TransportExceptionInterface $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, $translator->trans('api.reservations.notification.email_error', [], 'api'));
        }

        return $this->respondNoContent();
    }
}
