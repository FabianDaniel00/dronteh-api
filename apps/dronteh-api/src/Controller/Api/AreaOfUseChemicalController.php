<?php

namespace App\Controller\Api;

// use App\Entity\AreaOfUseChemical;
// use App\Repository\ChemicalRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\AreaOfUseChemicalRepository;
use Symfony\Component\Routing\Annotation\Route;
use Paknahad\JsonApiBundle\Controller\Controller;
// use App\JsonApi\Document\AreaOfUseChemical\AreaOfUseChemicalDocument;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
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
     * @Route("/chemicals/{chemical_id}", name="area_of_use_chemicals_index", methods="GET")
     */
    public function index(AreaOfUseChemicalRepository $areaOfUseChemicalRepository, ResourceCollection $resourceCollection, Request $request, int $chemical_id): Response
    {
        $resourceCollection->setRepository($areaOfUseChemicalRepository);

        $resourceCollection
            ->getQuery()
            ->join('r.chemical', 'c')
            ->where('r.chemical = :chemical_id')
            ->setParameter('chemical_id', $chemical_id)
            ->andWhere('c.is_deleted = 0');
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
    // public function show(AreaOfUseChemical $areaOfUseChemical, ChemicalRepository $chemicalRepository, Request $request): Response
    // {
    //     $isChemicalDeleted = $chemicalRepository->find($areaOfUseChemical->getChemical())->isDeleted();
    //     if($isChemicalDeleted) {
    //         throw $this->createNotFoundException('api.area_of_chemicals.not_found');
    //     }

    //     return $this->respondOk(
    //         new AreaOfUseChemicalDocument(new AreaOfUseChemicalResourceTransformer($request->getLocale())),
    //         $areaOfUseChemical
    //     );
    // }

    // /**
    //  * @Route("/{id}", name="area_of_use_chemicals_edit", methods="PATCH")
    //  */
    // public function edit(AreaOfUseChemical $areaOfUseChemical, Request $request, ChemicalRepository $chemicalRepository): Response
    // {
    //     $isChemicalDeleted = $chemicalRepository->find($areaOfUseChemical->getChemical())->isDeleted();
    //     if($isChemicalDeleted) {
    //         throw $this->createNotFoundException('api.area_of_chemicals.not_found');
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
    // public function delete(AreaOfUseChemical $areaOfUseChemical, ChemicalRepository $chemicalRepository): Response
    // {
    //     $isChemicalDeleted = $chemicalRepository->find($areaOfUseChemical->getChemical())->isDeleted();
    //     if($isChemicalDeleted) {
    //         throw $this->createNotFoundException('api.area_of_chemicals.not_found');
    //     }

    //     $this->entityManager->remove($areaOfUseChemical);
    //     $this->entityManager->flush();

    //     return $this->respondNoContent();
    // }
}
