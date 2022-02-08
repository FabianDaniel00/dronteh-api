<?php

namespace App\Controller\Api;

use App\Entity\Contact;
use App\JsonApi\Document\Contact\ContactDocument;
use App\JsonApi\Document\Contact\ContactsDocument;
use App\JsonApi\Hydrator\Contact\CreateContactHydrator;
use App\JsonApi\Hydrator\Contact\UpdateContactHydrator;
use App\JsonApi\Transformer\ContactResourceTransformer;
use App\Repository\ContactRepository;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/contacts")
 */
class ContactController extends Controller
{
    /**
     * @Route("/", name="contacts_index", methods="GET")
     */
    public function index(ContactRepository $contactRepository, ResourceCollection $resourceCollection): Response
    {
        $resourceCollection->setRepository($contactRepository);

        $resourceCollection->getQuery()->where('r.is_deleted = 0');
        $resourceCollection->handleIndexRequest();

        return $this->respondOk(
            new ContactsDocument(new ContactResourceTransformer()),
            $resourceCollection
        );
    }

    /**
     * @Route("/", name="contacts_new", methods="POST")
     */
    public function new(): Response
    {
        $contact = $this->jsonApi()->hydrate(
            new CreateContactHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            new Contact()
        );

        $this->validate($contact);

        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        return $this->respondOk(
            new ContactDocument(new ContactResourceTransformer()),
            $contact
        );
    }

    /**
     * @Route("/{id}", name="contacts_show", methods="GET")
     */
    public function show(Contact $contact): Response
    {
        if ($contact->isDeleted()) {
            throw $this->createNotFoundException("Can't find this contact message");
        }

        return $this->respondOk(
            new ContactDocument(new ContactResourceTransformer()),
            $contact
        );
    }

    /**
     * @Route("/{id}", name="contacts_edit", methods="PATCH")
     */
    public function edit(Contact $contact): Response
    {
        if ($contact->isDeleted()) {
            throw $this->createNotFoundException("Can't find this contact message");
        }

        $contact = $this->jsonApi()->hydrate(
            new UpdateContactHydrator($this->entityManager, $this->jsonApi()->getExceptionFactory()),
            $contact
        );

        $this->validate($contact);

        $this->entityManager->flush();

        return $this->respondOk(
            new ContactDocument(new ContactResourceTransformer()),
            $contact
        );
    }

    /**
     * @Route("/{id}", name="contacts_delete", methods="DELETE")
     */
    public function delete(Contact $contact): Response
    {
        if ($contact->isDeleted()) {
            throw $this->createNotFoundException("Can't find this contact message");
        }

        $contact->setIsDeleted(1);
        $this->entityManager->flush();

        return $this->respondNoContent();
    }
}
