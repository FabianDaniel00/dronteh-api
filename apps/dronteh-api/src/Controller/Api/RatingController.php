<?php

namespace App\Controller\Api;

use App\Entity\Rating;
use App\JsonApi\Document\Rating\RatingDocument;
use App\JsonApi\Document\Rating\RatingsDocument;
use App\JsonApi\Hydrator\Rating\CreateRatingHydrator;
use App\JsonApi\Hydrator\Rating\UpdateRatingHydrator;
use App\JsonApi\Transformer\RatingResourceTransformer;
use App\Repository\RatingRepository;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ratings")
 */
class RatingController extends Controller
{
    /**
     * @Route("/", name="ratings_index", methods="GET")
     */
    public function index(RatingRepository $ratingRepository, ResourceCollection $resourceCollection): Response
    {
        $resourceCollection->setRepository($ratingRepository);

        $resourceCollection->getQuery()->where('r.is_deleted = 0');
        $resourceCollection->handleIndexRequest();

        return $this->respondOk(
            new RatingsDocument(new RatingResourceTransformer()),
            $resourceCollection
        );
    }

    /**
     * @Route("/", name="ratings_new", methods="POST")
     */
    public function new(): Response
    {
        $rating = $this->jsonApi()->hydrate(
            new CreateRatingHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            new Rating()
        );

        $this->validate($rating);

        $this->entityManager->persist($rating);
        $this->entityManager->flush();

        return $this->respondOk(
            new RatingDocument(new RatingResourceTransformer()),
            $rating
        );
    }

    /**
     * @Route("/{id}", name="ratings_show", methods="GET")
     */
    public function show(Rating $rating): Response
    {
        if ($rating->isDeleted()) {
            throw $this->createNotFoundException("Can't find this rating");
        }

        return $this->respondOk(
            new RatingDocument(new RatingResourceTransformer()),
            $rating
        );
    }

    /**
     * @Route("/{id}", name="ratings_edit", methods="PATCH")
     */
    public function edit(Rating $rating): Response
    {
        if ($rating->isDeleted()) {
            throw $this->createNotFoundException("Can't find this rating");
        }

        $rating = $this->jsonApi()->hydrate(
            new UpdateRatingHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            $rating
        );

        $this->validate($rating);

        $this->entityManager->flush();

        return $this->respondOk(
            new RatingDocument(new RatingResourceTransformer()),
            $rating
        );
    }

    /**
     * @Route("/{id}", name="ratings_delete", methods="DELETE")
     */
    public function delete(Rating $rating): Response
    {
        if ($rating->isDeleted()) {
            throw $this->createNotFoundException("Can't find this rating");
        }

        $rating->setIsDeleted(1);
        $this->entityManager->flush();

        return $this->respondNoContent();
    }
}