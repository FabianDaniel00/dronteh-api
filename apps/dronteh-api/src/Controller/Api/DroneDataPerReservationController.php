<?php

namespace App\Controller\Api;

use App\Entity\DroneDataPerReservation;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Paknahad\JsonApiBundle\Controller\Controller;
use App\Repository\DroneDataPerReservationRepository;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\JsonApi\Transformer\DroneDataPerReservationResourceTransformer;
use App\JsonApi\Document\DroneDataPerReservation\DroneDataPerReservationDocument;
use App\JsonApi\Document\DroneDataPerReservation\DroneDataPerReservationsDocument;
// use App\JsonApi\Hydrator\DroneDataPerReservation\CreateDroneDataPerReservationHydrator;
// use App\JsonApi\Hydrator\DroneDataPerReservation\UpdateDroneDataPerReservationHydrator;

/**
 * @Route("/drone_data_per_reservations")
 */
class DroneDataPerReservationController extends Controller
{
    /**
     * @Route("/", name="drone_data_per_reservations_index", methods="GET")
     */
    public function index(DroneDataPerReservationRepository $droneDataPerReservationRepository, ResourceCollection $resourceCollection, Request $request, TranslatorInterface $translator): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            throw new AccessDeniedHttpException($translator->trans('api.current_user.null', [], 'api'));
        }

        $resourceCollection->setRepository($droneDataPerReservationRepository);

        $resourceCollection
            ->getQuery()
            ->join('r.reservation', 'r1')
            ->where('r.is_deleted = 0')
            ->andWhere('r1.is_deleted = 0')
            ->andWhere('r1.user = :user_id')
            ->setParameter('user_id', $currentUser->getId());
        $resourceCollection->handleIndexRequest();

        return $this->respondOk(
            new DroneDataPerReservationsDocument(new DroneDataPerReservationResourceTransformer($request->getLocale())),
            $resourceCollection
        );
    }

    // /**
    //  * @Route("/", name="drone_data_per_reservations_new", methods="POST")
    //  */
    // public function new(): Response
    // {
    //     $droneDataPerReservation = $this->jsonApi()->hydrate(
    //         new CreateDroneDataPerReservationHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
    //         new DroneDataPerReservation()
    //     );

    //     $this->validate($droneDataPerReservation);

    //     $this->entityManager->persist($droneDataPerReservation);
    //     $this->entityManager->flush();

    //     return $this->respondOk(
    //         new DroneDataPerReservationDocument(new DroneDataPerReservationResourceTransformer()),
    //         $droneDataPerReservation
    //     );
    // }

    /**
     * @Route("/{id}", name="drone_data_per_reservations_show", methods="GET")
     */
    public function show(DroneDataPerReservation $droneDataPerReservation, ReservationRepository $reservationRepository, Request $request, TranslatorInterface $translator): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            throw new AccessDeniedHttpException($translator->trans('api.current_user.null', [], 'api'));
        }

        $isReservationDeleted = $reservationRepository->find($droneDataPerReservation->getReservation())->isDeleted();
        if($isReservationDeleted || $droneDataPerReservation->isDeleted() || $droneDataPerReservation->getReservation()->getUser()->getId() !== $currentUser->getId()) {
            throw $this->createNotFoundException($translator->trans('api.drone_data_per_reservations.not_found', [], 'api'));
        }

        return $this->respondOk(
            new DroneDataPerReservationDocument(new DroneDataPerReservationResourceTransformer($request->getLocale())),
            $droneDataPerReservation
        );
    }

    // /**
    //  * @Route("/{id}", name="drone_data_per_reservations_edit", methods="PATCH")
    //  */
    // public function edit(DroneDataPerReservation $droneDataPerReservation, ReservationRepository $reservationRepository): Response
    // {
    //     $isReservationDeleted = $reservationRepository->find($droneDataPerReservation->getReservation())->isDeleted();
    //     if($isReservationDeleted || $droneDataPerReservation->isDeleted()) {
    //         throw $this->createNotFoundException('api.drone_data_per_reservations.not_found');
    //     }

    //     $droneDataPerReservation = $this->jsonApi()->hydrate(
    //         new UpdateDroneDataPerReservationHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
    //         $droneDataPerReservation
    //     );

    //     $this->validate($droneDataPerReservation);

    //     $this->entityManager->flush();

    //     return $this->respondOk(
    //         new DroneDataPerReservationDocument(new DroneDataPerReservationResourceTransformer()),
    //         $droneDataPerReservation
    //     );
    // }

    // /**
    //  * @Route("/{id}", name="drone_data_per_reservations_delete", methods="DELETE")
    //  */
    // public function delete(DroneDataPerReservation $droneDataPerReservation, ReservationRepository $reservationRepository): Response
    // {
    //     $isReservationDeleted = $reservationRepository->find($droneDataPerReservation->getReservation())->isDeleted();
    //     if($isReservationDeleted || $droneDataPerReservation->isDeleted()) {
    //         throw $this->createNotFoundException('api.drone_data_per_reservations.not_found');
    //     }

    //     $droneDataPerReservation->setIsDeleted(1);
    //     $this->entityManager->flush();

    //     return $this->respondNoContent();
    // }
}
