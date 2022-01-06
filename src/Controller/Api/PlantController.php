<?php

namespace App\Controller\Api;

use App\Entity\Plant;
use App\JsonApi\Document\Plant\PlantDocument;
use App\JsonApi\Document\Plant\PlantsDocument;
use App\JsonApi\Hydrator\Plant\CreatePlantHydrator;
use App\JsonApi\Hydrator\Plant\UpdatePlantHydrator;
use App\JsonApi\Transformer\PlantResourceTransformer;
use App\Repository\PlantRepository;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plants")
 */
class PlantController extends Controller
{
    /**
     * @Route("/", name="plants_index", methods="GET")
     */
    public function index(PlantRepository $plantRepository, ResourceCollection $resourceCollection): Response
    {
        $resourceCollection->setRepository($plantRepository);

        $resourceCollection->handleIndexRequest();

        return $this->respondOk(
            new PlantsDocument(new PlantResourceTransformer()),
            $resourceCollection
        );
    }

    /**
     * @Route("/", name="plants_new", methods="POST")
     */
    public function new(): Response
    {
        $plant = $this->jsonApi()->hydrate(
            new CreatePlantHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            new Plant()
        );

        $this->validate($plant);

        $this->entityManager->persist($plant);
        $this->entityManager->flush();

        return $this->respondOk(
            new PlantDocument(new PlantResourceTransformer()),
            $plant
        );
    }

    /**
     * @Route("/{id}", name="plants_show", methods="GET")
     */
    public function show(Plant $plant): Response
    {
        return $this->respondOk(
            new PlantDocument(new PlantResourceTransformer()),
            $plant
        );
    }

    /**
     * @Route("/{id}", name="plants_edit", methods="PATCH")
     */
    public function edit(Plant $plant): Response
    {
        $plant = $this->jsonApi()->hydrate(
            new UpdatePlantHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            $plant
        );

        $this->validate($plant);

        $this->entityManager->flush();

        return $this->respondOk(
            new PlantDocument(new PlantResourceTransformer()),
            $plant
        );
    }

    /**
     * @Route("/{id}", name="plants_delete", methods="DELETE")
     */
    public function delete(Plant $plant): Response
    {
        $this->entityManager->remove($plant);
        $this->entityManager->flush();

        return $this->respondNoContent();
    }
}