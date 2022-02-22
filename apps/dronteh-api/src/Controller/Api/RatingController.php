<?php

namespace App\Controller\Api;

use App\Entity\Rating;
use App\Repository\RatingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\JsonApi\Document\Rating\RatingDocument;
use Symfony\Component\Routing\Annotation\Route;
use App\JsonApi\Document\Rating\RatingsDocument;
use Paknahad\JsonApiBundle\Controller\Controller;
use App\JsonApi\Hydrator\Rating\CreateRatingHydrator;
// use App\JsonApi\Hydrator\Rating\UpdateRatingHydrator;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use App\JsonApi\Transformer\RatingResourceTransformer;

/**
 * @Route("/ratings")
 */
class RatingController extends Controller
{
    /**
     * @Route("/", name="ratings_index", methods="GET")
     */
    public function index(RatingRepository $ratingRepository, ResourceCollection $resourceCollection, Request $request): Response
    {
        $resourceCollection->setRepository($ratingRepository);

        $resourceCollection->getQuery()->where('r.is_deleted = 0');
        $resourceCollection->handleIndexRequest();

        return $this->respondOk(
            new RatingsDocument(new RatingResourceTransformer($request->getLocale())),
            $resourceCollection
        );
    }

    /**
     * @Route("/", name="ratings_new", methods="POST")
     */
    public function new(Request $request): Response
    {
        $rating = $this->jsonApi()->hydrate(
            new CreateRatingHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            new Rating()
        );

        $this->validate($rating);

        $this->entityManager->persist($rating);
        $this->entityManager->flush();

        return $this->respondOk(
            new RatingDocument(new RatingResourceTransformer($request->getLocale())),
            $rating
        );
    }

    /**
     * @Route("/{id}", name="ratings_show", methods="GET")
     */
    public function show(Rating $rating, Request $request): Response
    {
        if ($rating->isDeleted()) {
            throw $this->createNotFoundException('api.ratings.is_deleted');
        }

        return $this->respondOk(
            new RatingDocument(new RatingResourceTransformer($request->getLocale())),
            $rating
        );
    }

    // /**
    //  * @Route("/{id}", name="ratings_edit", methods="PATCH")
    //  */
    // public function edit(Rating $rating): Response
    // {
    //     if ($rating->isDeleted()) {
    //         throw $this->createNotFoundException('api.ratings.is_deleted');
    //     }

    //     $rating = $this->jsonApi()->hydrate(
    //         new UpdateRatingHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
    //         $rating
    //     );

    //     $this->validate($rating);

    //     $this->entityManager->flush();

    //     return $this->respondOk(
    //         new RatingDocument(new RatingResourceTransformer()),
    //         $rating
    //     );
    // }

    // /**
    //  * @Route("/{id}", name="ratings_delete", methods="DELETE")
    //  */
    // public function delete(Rating $rating): Response
    // {
    //     if ($rating->isDeleted()) {
    //         throw $this->createNotFoundException('api.ratings.is_deleted');
    //     }

    //     $rating->setIsDeleted(1);
    //     $this->entityManager->flush();

    //     return $this->respondNoContent();
    // }
}