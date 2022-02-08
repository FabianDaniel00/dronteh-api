<?php

namespace App\Controller\Api;

use App\Entity\Reservation;
use App\Entity\DroneDataPerReservation;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Paknahad\JsonApiBundle\Controller\Controller;
use App\Repository\DroneDataPerReservationRepository;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use App\JsonApi\Transformer\DroneDataPerReservationResourceTransformer;
use App\JsonApi\Document\DroneDataPerReservation\DroneDataPerReservationDocument;
use App\JsonApi\Document\DroneDataPerReservation\DroneDataPerReservationsDocument;
use App\JsonApi\Hydrator\DroneDataPerReservation\CreateDroneDataPerReservationHydrator;
use App\JsonApi\Hydrator\DroneDataPerReservation\UpdateDroneDataPerReservationHydrator;

/**
 * @Route("/drone/data/per/reservations")
 */
class DroneDataPerReservationController extends Controller
{
    /**
     * @Route("/", name="drone_data_per_reservations_index", methods="GET")
     */
    public function index(DroneDataPerReservationRepository $droneDataPerReservationRepository, ResourceCollection $resourceCollection): Response
    {
        $resourceCollection->setRepository($droneDataPerReservationRepository);

        $resourceCollection->getQuery()->where('r.is_deleted = 0');
        $resourceCollection->handleIndexRequest();

        return $this->respondOk(
            new DroneDataPerReservationsDocument(new DroneDataPerReservationResourceTransformer()),
            $resourceCollection
        );
    }

    /**
     * @Route("/", name="drone_data_per_reservations_new", methods="POST")
     */
    public function new(): Response
    {
        $droneDataPerReservation = $this->jsonApi()->hydrate(
            new CreateDroneDataPerReservationHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            new DroneDataPerReservation()
        );

        $this->validate($droneDataPerReservation);

        $this->entityManager->persist($droneDataPerReservation);
        $this->entityManager->flush();

        return $this->respondOk(
            new DroneDataPerReservationDocument(new DroneDataPerReservationResourceTransformer()),
            $droneDataPerReservation
        );
    }

    /**
     * @Route("/{id}", name="drone_data_per_reservations_show", methods="GET")
     */
    public function show(DroneDataPerReservation $droneDataPerReservation, ManagerRegistry $doctrine): Response
    {
        $isReservationDeleted = $doctrine->getManager()->getRepository(Reservation::class)->find($droneDataPerReservation->getReservation())->isDeleted();
        if($isReservationDeleted || $droneDataPerReservation->isDeleted()) {
            throw $this->createNotFoundException("Can't find this drone data");
        }

        return $this->respondOk(
            new DroneDataPerReservationDocument(new DroneDataPerReservationResourceTransformer()),
            $droneDataPerReservation
        );
    }

    /**
     * @Route("/{id}", name="drone_data_per_reservations_edit", methods="PATCH")
     */
    public function edit(DroneDataPerReservation $droneDataPerReservation, ManagerRegistry $doctrine): Response
    {
        $isReservationDeleted = $doctrine->getManager()->getRepository(Reservation::class)->find($droneDataPerReservation->getReservation())->isDeleted();
        if($isReservationDeleted || $droneDataPerReservation->isDeleted()) {
            throw $this->createNotFoundException("Can't find this drone data");
        }

        $droneDataPerReservation = $this->jsonApi()->hydrate(
            new UpdateDroneDataPerReservationHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            $droneDataPerReservation
        );

        $this->validate($droneDataPerReservation);

        $this->entityManager->flush();

        return $this->respondOk(
            new DroneDataPerReservationDocument(new DroneDataPerReservationResourceTransformer()),
            $droneDataPerReservation
        );
    }

    /**
     * @Route("/{id}", name="drone_data_per_reservations_delete", methods="DELETE")
     */
    public function delete(DroneDataPerReservation $droneDataPerReservation, ManagerRegistry $doctrine): Response
    {
        $isReservationDeleted = $doctrine->getManager()->getRepository(Reservation::class)->find($droneDataPerReservation->getReservation())->isDeleted();
        if($isReservationDeleted || $droneDataPerReservation->isDeleted()) {
            throw $this->createNotFoundException("Can't find this drone data");
        }

        $droneDataPerReservation->setIsDeleted(1);
        $this->entityManager->flush();

        return $this->respondNoContent();
    }
}