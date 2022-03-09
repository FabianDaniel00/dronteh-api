<?php

namespace App\Controller\Api;

use App\Entity\Chemical;
use App\Repository\ChemicalRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Paknahad\JsonApiBundle\Controller\Controller;
use App\JsonApi\Document\Chemical\ChemicalDocument;
use App\JsonApi\Document\Chemical\ChemicalsDocument;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\JsonApi\Transformer\ChemicalResourceTransformer;
// use App\JsonApi\Hydrator\Chemical\CreateChemicalHydrator;
// use App\JsonApi\Hydrator\Chemical\UpdateChemicalHydrator;

/**
 * @Route("/chemicals")
 */
class ChemicalController extends Controller
{
    /**
     * @Route("/", name="chemicals_index", methods="GET")
     */
    public function index(ChemicalRepository $chemicalRepository, ResourceCollection $resourceCollection, Request $request): Response
    {
        $resourceCollection->setRepository($chemicalRepository);

        $resourceCollection->getQuery()->where('r.is_deleted = 0');
        $resourceCollection->handleIndexRequest();

        return $this->respondOk(
            new ChemicalsDocument(new ChemicalResourceTransformer($request->getLocale())),
            $resourceCollection
        );
    }

    // /**
    //  * @Route("/", name="chemicals_new", methods="POST")
    //  */
    // public function new(Request $request): Response
    // {
    //     $chemical = $this->jsonApi()->hydrate(
    //         new CreateChemicalHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
    //         new Chemical()
    //     );

    //     $this->validate($chemical);

    //     $this->entityManager->persist($chemical);
    //     $this->entityManager->flush();

    //     return $this->respondOk(
    //         new ChemicalDocument(new ChemicalResourceTransformer($request->getLocale())),
    //         $chemical
    //     );
    // }

    /**
     * @Route("/{id}", name="chemicals_show", methods="GET")
     */
    public function show(Chemical $chemical, Request $request, TranslatorInterface $translator): Response
    {
        if ($chemical->isDeleted()) {
            throw $this->createNotFoundException($translator->trans('api.chemicals.not_found', [], 'api'));
        }

        return $this->respondOk(
            new ChemicalDocument(new ChemicalResourceTransformer($request->getLocale())),
            $chemical
        );
    }

    // /**
    //  * @Route("/{id}", name="chemicals_edit", methods="PATCH")
    //  */
    // public function edit(Chemical $chemical, Request $request): Response
    // {
    //     if ($chemical->isDeleted()) {
    //         throw $this->createNotFoundException('api.chemicals.not_found');
    //     }

    //     $chemical = $this->jsonApi()->hydrate(
    //         new UpdateChemicalHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
    //         $chemical
    //     );

    //     $this->validate($chemical);

    //     $this->entityManager->flush();

    //     return $this->respondOk(
    //         new ChemicalDocument(new ChemicalResourceTransformer($request->getLocale())),
    //         $chemical
    //     );
    // }

    // /**
    //  * @Route("/{id}", name="chemicals_delete", methods="DELETE")
    //  */
    // public function delete(Chemical $chemical): Response
    // {
    //     if ($chemical->isDeleted()) {
    //         throw $this->createNotFoundException('api.chemicals.not_found');
    //     }

    //     $chemical->setIsDeleted(1);
    //     $this->entityManager->flush();

    //     return $this->respondNoContent();
    // }
}
