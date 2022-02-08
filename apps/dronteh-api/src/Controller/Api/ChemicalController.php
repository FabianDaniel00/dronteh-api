<?php

namespace App\Controller\Api;

use App\Entity\Chemical;
use App\JsonApi\Document\Chemical\ChemicalDocument;
use App\JsonApi\Document\Chemical\ChemicalsDocument;
use App\JsonApi\Hydrator\Chemical\CreateChemicalHydrator;
use App\JsonApi\Hydrator\Chemical\UpdateChemicalHydrator;
use App\JsonApi\Transformer\ChemicalResourceTransformer;
use App\Repository\ChemicalRepository;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/chemicals")
 */
class ChemicalController extends Controller
{
    /**
     * @Route("/", name="chemicals_index", methods="GET")
     */
    public function index(ChemicalRepository $chemicalRepository, ResourceCollection $resourceCollection): Response
    {
        $resourceCollection->setRepository($chemicalRepository);

        $resourceCollection->getQuery()->where('r.is_deleted = 0');
        $resourceCollection->handleIndexRequest();

        return $this->respondOk(
            new ChemicalsDocument(new ChemicalResourceTransformer()),
            $resourceCollection
        );
    }

    /**
     * @Route("/", name="chemicals_new", methods="POST")
     */
    public function new(): Response
    {
        $chemical = $this->jsonApi()->hydrate(
            new CreateChemicalHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            new Chemical()
        );

        $this->validate($chemical);

        $this->entityManager->persist($chemical);
        $this->entityManager->flush();

        return $this->respondOk(
            new ChemicalDocument(new ChemicalResourceTransformer()),
            $chemical
        );
    }

    /**
     * @Route("/{id}", name="chemicals_show", methods="GET")
     */
    public function show(Chemical $chemical): Response
    {
        if ($chemical->isDeleted()) {
            throw $this->createNotFoundException("Can't find this chemical");
        }

        return $this->respondOk(
            new ChemicalDocument(new ChemicalResourceTransformer()),
            $chemical
        );
    }

    /**
     * @Route("/{id}", name="chemicals_edit", methods="PATCH")
     */
    public function edit(Chemical $chemical): Response
    {
        if ($chemical->isDeleted()) {
            throw $this->createNotFoundException("Can't find this chemical");
        }

        $chemical = $this->jsonApi()->hydrate(
            new UpdateChemicalHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            $chemical
        );

        $this->validate($chemical);

        $this->entityManager->flush();

        return $this->respondOk(
            new ChemicalDocument(new ChemicalResourceTransformer()),
            $chemical
        );
    }

    /**
     * @Route("/{id}", name="chemicals_delete", methods="DELETE")
     */
    public function delete(Chemical $chemical): Response
    {
        if ($chemical->isDeleted()) {
            throw $this->createNotFoundException("Can't find this chemical");
        }

        $chemical->setIsDeleted(1);
        $this->entityManager->flush();

        return $this->respondNoContent();
    }
}