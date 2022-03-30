<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use Symfony\Component\Translation\TranslatableMessage;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use App\Controller\Admin\AbstractRedirectCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use EasyCorp\Bundle\EasyAdminBundle\Exception\InsufficientEntityPermissionException;

abstract class AbstractUndeleteCrudController extends AbstractRedirectCrudController
{
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        $pageName = $responseParameters->get('pageName');
        $entityInstances = [];

        if (Crud::PAGE_DETAIL === $pageName || Crud::PAGE_EDIT === $pageName) {
            $entityInstances[] = $responseParameters->get('entity');
        } else if (Crud::PAGE_INDEX === $pageName) {
            $entityInstances = $responseParameters->get('entities');
        }

        foreach($entityInstances as $entity) {
            $entityInstance = $entity->getInstance();

            foreach($entity->getActions() as $action) {
                if ($action->getName() === 'undelete') {
                    $action->addHtmlAttributes([
                        'formaction' => $this->container->get(AdminUrlGenerator::class)->setAction('undelete')->setEntityId($entityInstance->getId())->removeReferrer()->generateUrl(),
                    ]);
                }
            }
        }

        return $responseParameters;
    }

    public function configureFields(string $pageName): iterable
    {
        $isDeleted = BooleanField::new('is_deleted', 'admin.list.is_deleted')
            ->renderAsSwitch(false)
            ->addCssClass('is-deleted-label')
            ->hideOnIndex()
            ->hideWhenCreating()
        ;

        if ($pageName === Crud::PAGE_EDIT) {
            $isDeleted->addCssClass('d-none');
        }

        return array_merge([
            $isDeleted,
        ], parent::configureFields($pageName));
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('is_deleted', $this->container->get('translator')->trans('admin.list.is_deleted', [], 'admin')))
        ;
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $entityInstance->setIsDeleted(1);
        $entityManager->flush();
    }

    public function undelete(AdminContext $context): Response
    {
        $event = new BeforeCrudActionEvent($context);
        $this->container->get('event_dispatcher')->dispatch($event);
        if ($event->isPropagationStopped()) {
            return $event->getResponse();
        }

        if (!$this->isGranted(Permission::EA_EXECUTE_ACTION, ['action' => 'undelete', 'entity' => $context->getEntity()])) {
            throw new ForbiddenActionException($context);
        }

        if (!$context->getEntity()->isAccessible()) {
            throw new InsufficientEntityPermissionException($context);
        }

        $csrfToken = $context->getRequest()->request->get('token');
        if (!$this->isCsrfTokenValid('ea-undelete', $csrfToken)) {
            return $this->redirectToRoute($context->getDashboardRouteName());
        }

        $entityInstance = $context->getEntity()->getInstance();

        $event = new BeforeEntityUpdatedEvent($entityInstance);
        $this->container->get('event_dispatcher')->dispatch($event);
        $entityInstance = $event->getEntityInstance();

        $this->undeleteEntity($entityInstance);

        $responseParameters = $this->configureResponseParameters(KeyValueStore::new([
            'entity' => $context->getEntity(),
        ]));

        $event = new AfterCrudActionEvent($context, $responseParameters);
        $this->container->get('event_dispatcher')->dispatch($event);
        if ($event->isPropagationStopped()) {
            return $event->getResponse();
        }

        $this->container->get('session')->getFlashBag()->add('success', new TranslatableMessage('admin.flash.entity.undeleted.success', [
            '%entity_name%' => $this->container->get('translator')->trans('admin.singular.reservation', [], 'admin'),
            '%entity%' => (string) $entityInstance
        ], 'admin'));

        if (null !== $referrer = $context->getReferrer()) {
            return $this->redirect($referrer);
        }

        return $this->redirect($this->container->get(AdminUrlGenerator::class)
            ->setAction(Action::EDIT)
            ->generateUrl()
        );
    }

    private function undeleteEntity(Object $entityInstance): void
    {
        $entityInstance->setIsDeleted(0);
        $this->container->get('doctrine')->getManager()->flush();
    }

    public function batchUndelete(AdminContext $context, BatchActionDto $batchActionDto): Response
    {
        $event = new BeforeCrudActionEvent($context);
        $this->container->get('event_dispatcher')->dispatch($event);
        if ($event->isPropagationStopped()) {
            return $event->getResponse();
        }

        if (!$this->isCsrfTokenValid('ea-batch-action-batchUndelete', $batchActionDto->getCsrfToken())) {
            return $this->redirectToRoute($context->getDashboardRouteName());
        }

        $entityManager = $this->container->get('doctrine')->getManagerForClass($batchActionDto->getEntityFqcn());
        $repository = $entityManager->getRepository($batchActionDto->getEntityFqcn());
        foreach ($batchActionDto->getEntityIds() as $entityId) {
            $entityInstance = $repository->find($entityId);
            if (!$entityInstance) {
                continue;
            }

            $entityDto = $context->getEntity()->newWithInstance($entityInstance);
            if (!$this->isGranted(Permission::EA_EXECUTE_ACTION, ['action' => 'undelete', 'entity' => $entityDto])) {
                throw new ForbiddenActionException($context);
            }

            if (!$entityDto->isAccessible()) {
                throw new InsufficientEntityPermissionException($context);
            }

            $event = new BeforeEntityUpdatedEvent($entityInstance);
            $this->container->get('event_dispatcher')->dispatch($event);
            $entityInstance = $event->getEntityInstance();

            $this->undeleteEntity($entityInstance);

            $this->container->get('session')->getFlashBag()->add('success', new TranslatableMessage('admin.flash.entity.undeleted.success', [
                '%entity_name%' => $this->container->get('translator')->trans('admin.singular.reservation', [], 'admin'),
                '%entity%' => (string) $entityInstance
            ], 'admin'));
        }

        $responseParameters = $this->configureResponseParameters(KeyValueStore::new([
            'entity' => $context->getEntity(),
            'batchActionDto' => $batchActionDto,
        ]));

        $event = new AfterCrudActionEvent($context, $responseParameters);
        $this->container->get('event_dispatcher')->dispatch($event);
        if ($event->isPropagationStopped()) {
            return $event->getResponse();
        }

        return $this->redirect($batchActionDto->getReferrerUrl());
    }
}
