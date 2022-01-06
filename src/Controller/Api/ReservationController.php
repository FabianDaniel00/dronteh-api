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
use App\JsonApi\Document\Reservation\ReservationDocument;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\JsonApi\Document\Reservation\ReservationsDocument;
use App\JsonApi\Transformer\ReservationResourceTransformer;
use App\JsonApi\Hydrator\Reservation\CreateReservationHydrator;
use App\JsonApi\Hydrator\Reservation\UpdateReservationHydrator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * @Route("/reservations")
 */
class ReservationController extends Controller
{
    /**
     * @Route("/", name="reservations_index", methods="GET")
     */
    public function index(ReservationRepository $reservationRepository, ResourceCollection $resourceCollection): Response
    {
        $resourceCollection->setRepository($reservationRepository);

        $resourceCollection->getQuery()->where('r.is_deleted = 0');
        $resourceCollection->handleIndexRequest();

        return $this->respondOk(
            new ReservationsDocument(new ReservationResourceTransformer()),
            $resourceCollection
        );
    }

    /**
     * @Route("/", name="reservations_new", methods="POST")
     */
    public function new(): Response
    {
        $reservation = $this->jsonApi()->hydrate(
            new CreateReservationHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            new Reservation()
        );

        $this->validate($reservation);

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $this->respondOk(
            new ReservationDocument(new ReservationResourceTransformer()),
            $reservation
        );
    }

    /**
     * @Route("/{id}", name="reservations_show", methods="GET")
     */
    public function show(Reservation $reservation): Response
    {
        if ($reservation->isDeleted()) {
            throw $this->createNotFoundException("Can't find this reservation");
        }

        return $this->respondOk(
            new ReservationDocument(new ReservationResourceTransformer()),
            $reservation
        );
    }

    /**
     * @Route("/{id}", name="reservations_edit", methods="PATCH")
     */
    public function edit(Reservation $reservation): Response
    {
        if ($reservation->isDeleted()) {
            throw $this->createNotFoundException("Can't find this reservation");
        }

        if ($reservation->getStatus() !== 0) {
            throw $this->createNotFoundException("Can't modify non active reservation");
        }

        $reservation = $this->jsonApi()->hydrate(
            new UpdateReservationHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            $reservation
        );

        $this->validate($reservation);

        $this->entityManager->flush();

        return $this->respondOk(
            new ReservationDocument(new ReservationResourceTransformer()),
            $reservation
        );
    }

    /**
     * @Route("/{id}", name="reservations_delete", methods="DELETE")
     */
    public function delete(Reservation $reservation): Response
    {
        if ($reservation->isDeleted()) {
            throw $this->createNotFoundException("Can't find this reservation");
        }

        $reservation->setIsDeleted(1);
        $this->entityManager->flush();

        return $this->respondNoContent();
    }

    /**
     * @Route("/send_notification", name="send_reservation_notification", methods="POST")
     */
    public function sendNotification(Request $request, ReservationRepository $reservationRepository, MailerInterface $mailer): Response
    {
        $id = $request->get('reservation_id');
        if($id === null) {
            throw new BadRequestHttpException("Can't find the reservation ID!");
        }

        $message = $request->get('message');
        if($message === null || $message === "") {
            throw new BadRequestHttpException("Can't find the reservation ID!");
        }

        $reservation = $reservationRepository->find($id);
        if($reservation === null) {
            return $this->createNotFoundException("Can't find the reservation!");
        }

        $name = $reservation->getUser()->getFirstname().' '.$reservation->getUser()->getLastname();

        $email = (new TemplatedEmail())
            ->from(new Address($this->getParameter('sender_email'), 'DronTeh Reservation'))
            ->to(new Address($reservation->getUser()->getEmail()))
            ->subject('DronTeh Reservation')

            // path of the Twig template to render
            ->htmlTemplate('notification/notification.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'message' => $message,
                'name' => $name,
            ]);
        
        try {
            $mailer->send($email);
        } catch(TransportExceptionInterface $e) {
            throw new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, "Email was not sent! ".$e->getMessage());
        }

        return new Response("Email sent successfully", Response::HTTP_OK);
    }
}