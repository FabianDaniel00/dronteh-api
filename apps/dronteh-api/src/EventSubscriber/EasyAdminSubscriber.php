<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private SessionInterface $session;
    private UserPasswordHasherInterface $userPasswordHasher;
    private TranslatorInterface $translator;

    public function __construct(SessionInterface $session, UserPasswordHasherInterface $userPasswordHasher, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['beforeEntityPersisted'],

            AfterEntityPersistedEvent::class => ['afterEntityPersisted'],
            AfterEntityUpdatedEvent::class => ['afterEntityUpdated'],
            AfterEntityDeletedEvent::class => ['afterEntityDeleted'],
        ];
    }

    public function beforeEntityPersisted(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof User) {
            $entity->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $entity,
                    $entity->getPassword()
                )
            );
        }
    }

    public function afterEntityPersisted(AfterEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        $this->putFlash($entity, 'admin.flash.entity.persisted.success');
    }

    public function afterEntityUpdated(AfterEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        $this->putFlash($entity, 'admin.flash.entity.updated.success');
    }

    public function afterEntityDeleted(AfterEntityDeletedEvent $event)
    {
        $entity = $event->getEntityInstance();

        $this->putFlash($entity, 'admin.flash.entity.deleted.success');
    }

    private function putFlash($entity, string $transData) {
        $entityName = explode('\\', get_class($entity));
        $entityName = strtolower($entityName[count($entityName) - 1]);

        $this->session->getFlashBag()->add('success', new TranslatableMessage($transData, [
            '%entity%' => $this->translator->trans('admin.singular.'.$entityName, [], 'admin')
        ], 'admin'));
    }
}
