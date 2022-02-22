<?php

namespace App\Controller\Api;

// use App\Entity\AreaOfUseChemical;
// use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\AreaOfUseChemicalRepository;
use Symfony\Component\Routing\Annotation\Route;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
// use App\JsonApi\Document\AreaOfUseChemical\AreaOfUseChemicalDocument;
use App\JsonApi\Transformer\AreaOfUseChemicalResourceTransformer;
use App\JsonApi\Document\AreaOfUseChemical\AreaOfUseChemicalsDocument;
// use App\JsonApi\Hydrator\AreaOfUseChemical\UpdateAreaOfUseChemicalHydrator;
// use App\JsonApi\Hydrator\AreaOfUseChemical\CreateAreaOfUseChemicalHydrator;

/**
 * @Route("/area_of_use_chemicals")
 */
class AreaOfUseChemicalController extends Controller
{
    /**
     * @Route("/{chemical_id}", name="area_of_use_chemicals_index", methods="GET")
     */
    public function index(AreaOfUseChemicalRepository $areaOfUseChemicalRepository, ResourceCollection $resourceCollection, Request $request, int $chemical_id): Response
    {
        $resourceCollection->setRepository($areaOfUseChemicalRepository);

        $resourceCollection
            ->getQuery()
            ->where('r.chemical = :chemical_id')
            ->setParameter('chemical_id', $chemical_id);
        $resourceCollection->handleIndexRequest();

        return $this->respondOk(
            new AreaOfUseChemicalsDocument(new AreaOfUseChemicalResourceTransformer($request->getLocale())),
            $resourceCollection
        );
    }

    // /**
    //  * @Route("/", name="area_of_use_chemicals_new", methods="POST")
    //  */
    // public function new(Request $request): Response
    // {
    //     $areaOfUseChemical = $this->jsonApi()->hydrate(
    //         new CreateAreaOfUseChemicalHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
    //         new AreaOfUseChemical()
    //     );

    //     $this->validate($areaOfUseChemical);

    //     $this->entityManager->persist($areaOfUseChemical);
    //     $this->entityManager->flush();

    //     return $this->respondOk(
    //         new AreaOfUseChemicalDocument(new AreaOfUseChemicalResourceTransformer($request->getLocale())),
    //         $areaOfUseChemical
    //     );
    // }

    // /**
    //  * @Route("/{id}", name="area_of_use_chemicals_show", methods="GET")
    //  */
    // public function show(AreaOfUseChemical $areaOfUseChemical, ManagerRegistry $doctrine, Request $request): Response
    // {
    //     $isChemicalDeleted = $doctrine->getManager()->getRepository(Chemical::class)->find($areaOfUseChemical->getChemical())->isDeleted();
    //     if($isChemicalDeleted) {
    //         throw $this->createNotFoundException('api.area_of_chemicals.is_deleted');
    //     }

    //     return $this->respondOk(
    //         new AreaOfUseChemicalDocument(new AreaOfUseChemicalResourceTransformer($request->getLocale())),
    //         $areaOfUseChemical
    //     );
    // }

    // /**
    //  * @Route("/{id}", name="area_of_use_chemicals_edit", methods="PATCH")
    //  */
    // public function edit(AreaOfUseChemical $areaOfUseChemical, Request $request, ManagerRegistry $doctrine): Response
    // {
    //     $isChemicalDeleted = $doctrine->getManager()->getRepository(Chemical::class)->find($areaOfUseChemical->getChemical())->isDeleted();
    //     if($isChemicalDeleted) {
    //         throw $this->createNotFoundException('api.area_of_chemicals.is_deleted');
    //     }

    //     $areaOfUseChemical = $this->jsonApi()->hydrate(
    //         new UpdateAreaOfUseChemicalHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
    //         $areaOfUseChemical
    //     );

    //     $this->validate($areaOfUseChemical);

    //     $this->entityManager->flush();

    //     return $this->respondOk(
    //         new AreaOfUseChemicalDocument(new AreaOfUseChemicalResourceTransformer($request->getLocale())),
    //         $areaOfUseChemical
    //     );
    // }

    // /**
    //  * @Route("/{id}", name="area_of_use_chemicals_delete", methods="DELETE")
    //  */
    // public function delete(AreaOfUseChemical $areaOfUseChemical, ManagerRegistry $doctrine): Response
    // {
    //     $isChemicalDeleted = $doctrine->getManager()->getRepository(Chemical::class)->find($areaOfUseChemical->getChemical())->isDeleted();
    //     if($isChemicalDeleted) {
    //         throw $this->createNotFoundException('api.area_of_chemicals.is_deleted');
    //     }

    //     $this->entityManager->remove($areaOfUseChemical);
    //     $this->entityManager->flush();

    //     return $this->respondNoContent();
    // }
}
