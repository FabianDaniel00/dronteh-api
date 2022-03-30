<?php

namespace App\Controller\Api;

use IntlDateFormatter;
use App\Entity\Reservation;
use Symfony\Component\Mime\Address;
use App\Repository\ReservationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Paknahad\JsonApiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\JsonApi\Document\Reservation\ReservationDocument;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\JsonApi\Document\Reservation\ReservationsDocument;
use App\JsonApi\Transformer\ReservationResourceTransformer;
use App\JsonApi\Hydrator\Reservation\CreateReservationHydrator;
use App\JsonApi\Hydrator\Reservation\UpdateReservationHydrator;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

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
     * @Route("/get_set_time_info/{id}", name="get_set_time_info", methods="GET")
     */
    public function getSetTimeInfo(Reservation $reservation, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('setTime-'.$reservation->getId(), $request->headers->get('x-csrf-token'))) {
            // throw new InvalidCsrfTokenException();
        }

        $formatter = new IntlDateFormatter($request->getLocale(), IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE);
        $intervalStart = $formatter->format($reservation->getReservationIntervalStart());
        $intervalEnd = $formatter->format($reservation->getReservationIntervalEnd());
        $time = $reservation->getTime();

        return new JsonResponse([
            'data' => [
                'time' => $time ? $time->format(\DATE_ATOM) : null,
                'interval_start' => $intervalStart,
                'interval_end' => $intervalEnd,
            ]
        ]);
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

        $reservation->setUser($currentUser);

        $this->checkIntervalDifference(7, $reservation, $translator);

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

        $this->checkIntervalDifference(7, $reservation, $translator);

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

    private function checkIntervalDifference(int $differenceBetweenInterval, Reservation $reservation, TranslatorInterface $translator): void {
        if ($reservation->getReservationIntervalStart()->modify('+'.$differenceBetweenInterval.' days') > $reservation->getReservationIntervalEnd()) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, $translator->trans('api.reservations.edit.less_difference', ['%day%' => $differenceBetweenInterval], 'api'));
        }

        if ($reservation->getReservationIntervalStart() < new \DateTime('now') ) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, $translator->trans('api.reservations.edit.start_interval', [], 'api'));
        }
    }
}
