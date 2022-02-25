<?php

namespace App\Controller\Api;

use App\Entity\Plant;
use App\Repository\PlantRepository;
use App\JsonApi\Document\Plant\PlantDocument;
use Symfony\Component\HttpFoundation\Request;
use App\JsonApi\Document\Plant\PlantsDocument;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Paknahad\JsonApiBundle\Controller\Controller;
// use App\JsonApi\Hydrator\Plant\CreatePlantHydrator;
// use App\JsonApi\Hydrator\Plant\UpdatePlantHydrator;
use App\JsonApi\Transformer\PlantResourceTransformer;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;

/**
 * @Route("/plants")
 */
class PlantController extends Controller
{
    /**
     * @Route("/", name="plants_index", methods="GET")
     */
    public function index(PlantRepository $plantRepository, ResourceCollection $resourceCollection, Request $request): Response
    {
        $resourceCollection->setRepository($plantRepository);

        $resourceCollection->getQuery()->where('r.is_deleted = 0');
        $resourceCollection->handleIndexRequest();

        return $this->respondOk(
            new PlantsDocument(new PlantResourceTransformer($request->getLocale())),
            $resourceCollection
        );
    }

    // /**
    //  * @Route("/", name="plants_new", methods="POST")
    //  */
    // public function new(Request $request): Response
    // {
    //     $plant = $this->jsonApi()->hydrate(
    //         new CreatePlantHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
    //         new Plant()
    //     );

    //     $this->validate($plant);

    //     $this->entityManager->persist($plant);
    //     $this->entityManager->flush();

    //     return $this->respondOk(
    //         new PlantDocument(new PlantResourceTransformer($request->getLocale())),
    //         $plant
    //     );
    // }

    /**
     * @Route("/{id}", name="plants_show", methods="GET")
     */
    public function show(Plant $plant, Request $request): Response
    {
        if ($plant->isDeleted()) {
            throw $this->createNotFoundException('api.plants.not_found');
        }

        return $this->respondOk(
            new PlantDocument(new PlantResourceTransformer($request->getLocale())),
            $plant
        );
    }

    // /**
    //  * @Route("/{id}", name="plants_edit", methods="PATCH")
    //  */
    // public function edit(Plant $plant, Request $request): Response
    // {
    //     if ($plant->isDeleted()) {
    //         throw $this->createNotFoundException('api.plants.not_found');
    //     }

    //     $plant = $this->jsonApi()->hydrate(
    //         new UpdatePlantHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
    //         $plant
    //     );

    //     $this->validate($plant);

    //     $this->entityManager->flush();

    //     return $this->respondOk(
    //         new PlantDocument(new PlantResourceTransformer($request->getLocale())),
    //         $plant
    //     );
    // }

    // /**
    //  * @Route("/{id}", name="plants_delete", methods="DELETE")
    //  */
    // public function delete(Plant $plant): Response
    // {
    //     if ($plant->isDeleted()) {
    //         throw $this->createNotFoundException('api.plnts.not_found');
    //     }

    //     $plant->setIsDeleted(1);
    //     $this->entityManager->flush();

    //     return $this->respondNoContent();
    // }
}
