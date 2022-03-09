<?php

namespace App\EventSubscriber;

// use App\Entity\User;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return [
            AfterEntityPersistedEvent::class => ['afterEntityPersisted'],
            AfterEntityUpdatedEvent::class => ['afterEntityUpdated'],
            AfterEntityDeletedEvent::class => ['afterEntityDeleted'],
        ];
    }

    public function afterEntityPersisted(AfterEntityPersistedEvent $event)
    {
        // $entity = $event->getEntityInstance();

        // if ($entity instanceof User) {

        // }
        $this->session->getFlashBag()->add('success', new TranslatableMessage('admin.flash.entity.persisted.success', ['%entity%' => (string) $event->getEntityInstance()], 'admin'));
    }

    public function afterEntityUpdated(AfterEntityUpdatedEvent $event)
    {
        $this->session->getFlashBag()->add('success', new TranslatableMessage('admin.flash.entity.updated.success', ['%entity%' => (string) $event->getEntityInstance()], 'admin'));
    }

    public function afterEntityDeleted(AfterEntityDeletedEvent $event)
    {
        $this->session->getFlashBag()->add('success', new TranslatableMessage('admin.flash.entity.deleted.success', ['%entity%' => (string) $event->getEntityInstance()], 'admin'));
    }
}
