<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Plant;
use App\Entity\Chemical;
use App\Entity\Reservation;
use App\Form\Type\SetTimeType;
use Doctrine\ORM\QueryBuilder;
use App\Repository\UserRepository;
use App\Repository\PlantRepository;
use Symfony\Component\Mime\Address;
use App\Repository\ChemicalRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Controller\Admin\UserCrudController;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Controller\Admin\PlantCrudController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Admin\ChemicalCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NullFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use App\Controller\Admin\AbstractUndeleteCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use App\Controller\Admin\Filter\DateTimeFilter as ReservationDateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Exception\InsufficientEntityPermissionException;

class ReservationCrudController extends AbstractUndeleteCrudController
{
    private AdminUrlGenerator $adminUrlGenerator;
    private TranslatorInterface $translator;
    private ManagerRegistry $doctrine;
    private AdminContextProvider $adminContextProvider;
    private EventDispatcher $dispatcher;
    private MailerInterface $mailer;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, TranslatorInterface $translator, ManagerRegistry $doctrine, AdminContextProvider $adminContextProvider, MailerInterface $mailer)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->translator = $translator;
        $this->doctrine = $doctrine;
        $this->adminContextProvider = $adminContextProvider;
        $this->dispatcher = new EventDispatcher();
        $this->mailer = $mailer;
    }

    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

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
                if ($action->getName() === 'setTime') {
                    $entityId = $entityInstance->getId();

                    $action->addHtmlAttributes([
                        'formaction' => $this->adminUrlGenerator->setAction('setTime')->setEntityId($entityId)->removeReferrer()->generateUrl(),
                        'fetchurl' => $this->generateUrl('get_set_time_info', [
                            'id' => $entityId,
                        ], false),
                        'csrf-token' => $this->container->get('security.csrf.token_manager')->refreshToken('setTime-'.$entityId)->getValue()
                    ]);
                }
            }
        }

        $responseParameters->set('set_time_form', $this->createForm(SetTimeType::class)->createView());

        return parent::configureResponseParameters($responseParameters);
    }

    public function configureFields(string $pageName): iterable
    {
        $toBePresent = BooleanField::new('to_be_present', 'admin.list.reservations.to_be_present')->hideWhenCreating()->hideOnIndex();
        $time = DateTimeField::new('time', 'admin.list.reservations.time');
        $createdAt = DateTimeField::new('created_at', 'admin.list.created_at');
        $updatedAt = DateTimeField::new('updated_at', 'admin.list.updated_at');

        if ($pageName !== Crud::PAGE_EDIT) {
            $toBePresent->renderAsSwitch(false);
            $time->hideWhenCreating();
            $createdAt->hideWhenCreating();
            $updatedAt->hideWhenCreating();
        } else {
            $time->addCssClass('d-none');
            $createdAt->addCssClass('d-none');
            $updatedAt->addCssClass('d-none');
        }

        $statuses = [];
        for ($i = 0; $i <= 2; $i++) {
            $statuses['admin.list.reservations.statuses.'.$i] = $i;
        }

        $locale = $this->adminContextProvider->getContext()->getRequest()->getLocale();

        return array_merge(parent::configureFields($pageName), [
            AssociationField::new('user', 'admin.singular.user')
                ->setCrudController(UserCrudController::class)
                ->autocomplete()
                ->setQueryBuilder(
                    fn (QueryBuilder $queryBuilder) => $queryBuilder
                        ->getEntityManager()
                        ->getRepository(User::class)
                        ->findBy([
                            'is_deleted' => false,
                        ], [
                            'firstname' => 'ASC',
                            'lastname' => 'ASC',
                        ])
                )
            ,
            AssociationField::new('chemical', 'admin.singular.chemical')
                ->setCrudController(ChemicalCrudController::class)
                ->autocomplete()
                ->setQueryBuilder(
                    fn (QueryBuilder $queryBuilder) => $queryBuilder
                        ->getEntityManager()
                        ->getRepository(Chemical::class)
                        ->findBy([
                            'is_deleted' => false,
                        ], [
                            'name_'.$locale => 'ASC',
                        ])
                )
            ,
            AssociationField::new('plant', 'admin.singular.plant')
                ->setCrudController(PlantCrudController::class)
                ->autocomplete()
                ->setQueryBuilder(
                    fn (QueryBuilder $queryBuilder) => $queryBuilder
                        ->getEntityManager()
                        ->getRepository(Plant::class)
                        ->findBy([
                            'is_deleted' => false,
                        ], [
                            'name_'.$locale => 'ASC',
                        ])
                )
            ,
            TextField::new('parcel_number', 'admin.list.reservations.parcel_number'),
            TextField::new('gps_coordinates', 'admin.list.reservations.gps_coordinates'),
            NumberField::new('land_area', 'admin.list.reservations.land_area'),
            ChoiceField::new('status', 'admin.list.reservations.status')
                ->setChoices($statuses)
                ->setHelp('admin.list.reservations.help.status')
                ->hideWhenCreating()
            ,
            $toBePresent,
            $time,
            DateField::new('reservation_interval_start', 'admin.list.reservations.reservation_interval_start')->setColumns(4)->hideOnIndex(),
            DateField::new('reservation_interval_end', 'admin.list.reservations.reservation_interval_end')->setColumns(4)->hideOnIndex(),
            FormField::addRow(),
            TextareaField::new('comment', 'admin.list.reservations.comment')->setMaxLength(5000)->setNumOfRows(6)->stripTags()->hideOnIndex(),
            $createdAt,
            $updatedAt,
        ]);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('admin.singular.reservation')
            ->setEntityLabelInPlural('admin.plural.reservation')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        $setTime = Action::new('setTime', 'admin.buttons.set_time', 'fas fa-calendar-alt')
            ->linkToCrudAction('setTime')
            ->addCssClass('action-setTime btn btn-warning')
            ->setHtmlAttributes([
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#modal-set-time',
            ])
            ->displayIf(static function ($entity) {
                return !$entity->isDeleted() && $entity->getStatus() !== 2;
            })
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, $setTime)
            ->add(Crud::PAGE_EDIT, $setTime)
            ->add(Crud::PAGE_DETAIL, $setTime)

            ->reorder(Crud::PAGE_EDIT, [
                Action::INDEX,
                Action::NEW,
                Action::DELETE,
                'undelete',
                Action::DETAIL,
                Action::SAVE_AND_CONTINUE,
                'saveAndViewDetail',
                'setTime',
                Action::SAVE_AND_RETURN,
            ])
            ->reorder(Crud::PAGE_DETAIL, [Action::INDEX, Action::NEW, Action::EDIT, 'setTime', Action::DELETE, 'undelete'])

            ->update(Crud::PAGE_INDEX, 'setTime',
                fn (Action $action) => $action->setCssClass('action-setTime'))
        ;
    }

    public function setTime(AdminContext $context): Response
    {
        $event = new BeforeCrudActionEvent($context);
        $this->dispatcher->dispatch($event);
        if ($event->isPropagationStopped()) {
            return $event->getResponse();
        }

        if (!$this->isGranted(Permission::EA_EXECUTE_ACTION, ['action' => 'setTime', 'entity' => $context->getEntity()])) {
            throw new ForbiddenActionException($context);
        }

        if (!$context->getEntity()->isAccessible()) {
            throw new InsufficientEntityPermissionException($context);
        }

        $entityInstance = $context->getEntity()->getInstance();
        $request = $context->getRequest();

        if ($request->isXmlHttpRequest()) {
            $fieldName = $request->query->get('fieldName');
            $newValue = 'true' === mb_strtolower($request->query->get('newValue'));

            $event = $this->ajaxEdit($context->getEntity(), $fieldName, $newValue);
            if ($event->isPropagationStopped()) {
                return $event->getResponse();
            }

            // cast to integer instead of string to avoid sending empty responses for 'false'
            return new Response((int) $newValue);
        }

        $time = new \DateTime($request->request->get('set_time')['time']);
        $time = new \DateTime($time->format('Y-m-d H:i'));
        $formattedTime = new \DateTime($time->format('Y-m-d'));
        $intervalStart = $entityInstance->getReservationIntervalStart();
        $intervalEnd = $entityInstance->getReservationIntervalEnd();

        if ($intervalStart > $formattedTime || $intervalEnd < $formattedTime) {
            $this->addFlash('danger', new TranslatableMessage('admin.flash.reservations.set_time.time_between_interval', [
                '%interval_start%' => $intervalStart,
                '%interval_end%' => $intervalEnd,
            ], 'admin'));

            $url = $this->adminUrlGenerator
                ->setAction(Action::EDIT)
                ->generateUrl()
            ;

            return $this->redirect($url);
        }

        $setTimeForm = $this->createForm(SetTimeType::class, [
            'time' => $time,
        ]);
        $setTimeForm->handleRequest($request);
        if ($setTimeForm->isSubmitted() && $setTimeForm->isValid()) {
            $this->processUploadedFiles($setTimeForm);

            $event = new BeforeEntityUpdatedEvent($entityInstance);
            $this->dispatcher->dispatch($event);
            $entityInstance = $event->getEntityInstance();

            $this->setTimeAndSendNotification($setTimeForm->getData()['time'], $entityInstance);

            $this->addFlash('success', new TranslatableMessage('admin.flash.reservations.set_time.success', [
                '%reservation%' => (string) $entityInstance
            ], 'admin'));

            $url = $this->adminUrlGenerator
                ->setAction(Action::DETAIL)
                ->generateUrl()
            ;

            return $this->redirect($url);
        }

        $responseParameters = $this->configureResponseParameters(KeyValueStore::new([
            'entity' => $context->getEntity(),
        ]));

        $event = new AfterCrudActionEvent($context, $responseParameters);
        $this->dispatcher->dispatch($event);
        if ($event->isPropagationStopped()) {
            return $event->getResponse();
        }

        if (null !== $referrer = $context->getReferrer()) {
            return $this->redirect($referrer);
        }

        return $this->redirect($this->adminUrlGenerator->setAction(Action::DETAIL)->generateUrl());
    }

    private function setTimeAndSendNotification(?\DateTime $time, Reservation $reservation): void
    {
        $reservation->setTime($time);
        $reservation->setStatus(1);
        $this->doctrine->getManager()->flush();

        $userReservation = $reservation->getUser();

        $locale = $userReservation->getLocale();

        $email = (new TemplatedEmail())
            ->from(new Address($this->getParameter('sender_email'), $this->translator->trans('mails.reservation_notification.name', [], 'mails', $locale)))
            ->to(new Address($userReservation->getEmail()))
            ->subject($this->translator->trans('mails.reservation_notification.subject', ['%parcel_number%' => $reservation->getParcelNumber()], 'mails', $locale))

            // path of the Twig template to render
            ->htmlTemplate('reservation/reservation_notification.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'name' => $userReservation->getFirstname().' '.$userReservation->getLastname(),
                'time' => $time,
                'reservation' => $reservation,
                'locale' => $locale,
                'new_reservation' => false,
            ]);

        try {
            $this->mailer->send($email);
        } catch(TransportExceptionInterface $e) {
            $this->addFlash('danger', new TranslatableMessage('admin.flash.reservations.set_time.error', [
                '%entity%' => (string) $reservation
            ], 'admin'));
        }
    }

    public function configureFilters(Filters $filters): Filters
    {
        $statuses = [];
        for ($i = 0; $i <= 2; $i++) {
            $statuses[$this->translator->trans('admin.list.reservations.statuses.'.$i, [], 'admin')] = $i;
        }

        return parent::configureFilters($filters)
            ->add(EntityFilter::new('user', $this->translator->trans('admin.singular.user', [], 'admin'))
                ->setFormTypeOption('value_type_options', [
                    'multiple' => true,
                    'query_builder' => function (UserRepository $repository) {
                        return $repository
                            ->createQueryBuilder('r')
                            ->where('r.is_deleted = 0')
                            ->orderBy('r.firstname', 'ASC')
                        ;
                    }
                ])
            )
            ->add(EntityFilter::new('plant', $this->translator->trans('admin.singular.plant', [], 'admin'))
                ->setFormTypeOption('value_type_options', [
                    'multiple' => true,
                    'query_builder' => function (PlantRepository $repository) {
                        return $repository
                            ->createQueryBuilder('r')
                            ->where('r.is_deleted = 0')
                            ->orderBy('r.name_'.$this->adminContextProvider->getContext()->getRequest()->getLocale(), 'ASC')
                        ;
                    }
                ])
            )
            ->add(EntityFilter::new('chemical', $this->translator->trans('admin.singular.chemical', [], 'admin'))
                ->setFormTypeOption('value_type_options', [
                    'multiple' => true,
                    'query_builder' => function (ChemicalRepository $repository) {
                        return $repository
                            ->createQueryBuilder('r')
                            ->where('r.is_deleted = 0')
                            ->orderBy('r.name_'.$this->adminContextProvider->getContext()->getRequest()->getLocale(), 'ASC')
                        ;
                    }
                ])
            )
            ->add(ReservationDateTimeFilter::new('time', $this->translator->trans('admin.list.reservations.time', [], 'admin')))
            ->add(NumericFilter::new('parcel_number', $this->translator->trans('admin.list.reservations.parcel_number', [], 'admin')))
            ->add(TextFilter::new('gps_coordinates', $this->translator->trans('admin.list.reservations.gps_coordinates', [], 'admin')))
            ->add(NumericFilter::new('land_area', $this->translator->trans('admin.list.reservations.land_area', [], 'admin')))
            ->add(ChoiceFilter::new('status', $this->translator->trans('admin.list.reservations.status', [], 'admin'))
                ->setChoices($statuses)
                ->canSelectMultiple()
            )
            ->add(BooleanFilter::new('to_be_present', $this->translator->trans('admin.list.reservations.to_be_present', [], 'admin')))
            ->add(NullFilter::new('comment', $this->translator->trans('admin.list.reservations.comment', [], 'admin'))
                ->setChoiceLabels($this->translator->trans('admin.null', [], 'admin'), $this->translator->trans('admin.not_null', [], 'admin'))
            )
            ->add(DateTimeFilter::new('reservation_interval_start', $this->translator->trans('admin.list.reservations.reservation_interval_start', [], 'admin')))
            ->add(DateTimeFilter::new('reservation_interval_end', $this->translator->trans('admin.list.reservations.reservation_interval_end', [], 'admin')))
            ->add(DateTimeFilter::new('created_at', $this->translator->trans('admin.list.created_at', [], 'admin')))
            ->add(DateTimeFilter::new('updated_at', $this->translator->trans('admin.list.updated_at', [], 'admin')))
        ;
    }
}
