<?php

namespace MauticPlugin\MauticEventsBundle\Controller\Api;

use Doctrine\Persistence\ManagerRegistry;
use Mautic\ApiBundle\Controller\CommonApiController;
use Mautic\ApiBundle\Helper\EntityResultHelper;
use Mautic\CoreBundle\Factory\ModelFactory;
use Mautic\CoreBundle\Helper\AppVersion;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\CoreBundle\Translation\Translator;
use MauticPlugin\MauticEventsBundle\Entity\Event;
use MauticPlugin\MauticEventsBundle\Entity\EventContact;
use MauticPlugin\MauticEventsBundle\Model\EventModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * @extends CommonApiController<Event>
 */
class EventApiController extends CommonApiController
{
    /**
     * @var EventModel|null
     */
    protected $model;

    public function __construct(
        CorePermissions $security,
        Translator $translator,
        EntityResultHelper $entityResultHelper,
        RouterInterface $router,
        FormFactoryInterface $formFactory,
        AppVersion $appVersion,
        RequestStack $requestStack,
        ManagerRegistry $doctrine,
        ModelFactory $modelFactory,
        EventDispatcherInterface $dispatcher,
        CoreParametersHelper $coreParametersHelper,
    ) {
        $eventModel = $modelFactory->getModel('event');
        \assert($eventModel instanceof EventModel);

        $this->model           = $eventModel;
        $this->entityClass     = Event::class;
        $this->entityNameOne   = 'event';
        $this->entityNameMulti = 'events';
        $this->permissionBase  = 'events:events';
        $this->serializerGroups = ['eventDetails'];

        parent::__construct(
            $security,
            $translator,
            $entityResultHelper,
            $router,
            $formFactory,
            $appVersion,
            $requestStack,
            $doctrine,
            $modelFactory,
            $dispatcher,
            $coreParametersHelper
        );
    }

    /**
     * Normalize incoming parameters (accept snake_case for convenience).
     *
     * @param array<mixed> $parameters
     */
    protected function prepareParametersForBinding(Request $request, $parameters, $entity, $action)
    {
        // Define snake_case to camelCase mapping for all fields
        $fieldMapping = [
            'event_external_id' => 'eventExternalId',
            'event_program_url_c' => 'eventProgramUrlC',
            'history_url_c' => 'historyUrlC',
            'event_speakers_url_c' => 'eventSpeakersUrlC',
            'submission_url_c' => 'submissionUrlC',
            'event_faq_url_c' => 'eventFaqUrlC',
            'event_venue_url_c' => 'eventVenueUrlC',
            'visa_url_c' => 'visaUrlC',
            'registration_url_c' => 'registrationUrlC',
            'event_facebook_url_c' => 'eventFacebookUrlC',
            'event_feedback_url_c' => 'eventFeedbackUrlC',
            'event_testimonials_url_c' => 'eventTestimonialsUrlC',
            'website_url_c' => 'websiteUrlC',
            'easy_payment_url_c' => 'easyPaymentUrlC',
            'decline_redirect' => 'declineRedirect',
            'accept_redirect' => 'acceptRedirect',
            'event_manager_email_c' => 'eventManagerEmailC',
            'isbn_number_c' => 'isbnNumberC',
            'event_wire_transfer_data_c' => 'eventWireTransferDataC',
            'about_event_c' => 'aboutEventC',
            'duration_minutes' => 'durationMinutes',
            'duration_hours' => 'durationHours',
            'abstract_book_image_c' => 'abstractBookImageC',
            'date_end' => 'dateEnd',
            'date_start' => 'dateStart',
            'date_modified' => 'dateModified',
            'date_entered' => 'dateEntered',
            'early_bird_reg_deadline_c' => 'earlyBirdRegDeadlineC',
            'event_start_date_c' => 'eventStartDateC',
            'submission_deadline_c' => 'submissionDeadlineC',
            'event_end_date_c' => 'eventEndDateC',
            'early_reg_deadline_c' => 'earlyRegDeadlineC',
            'final_reg_deadline_c' => 'finalRegDeadlineC',
            'currency_id' => 'currencyId',
            'event_organizer_c' => 'eventOrganizerC',
            'event_round_c' => 'eventRoundC',
            'event_manager_name_c' => 'eventManagerNameC',
            'event_city_c' => 'eventCityC',
            'event_field_c' => 'eventFieldC',
            'event_country_c' => 'eventCountryC',
            'activity_status_type' => 'activityStatusType',
            'invite_templates' => 'inviteTemplates',
            'created_at' => 'createdAt',
            'updated_at' => 'updatedAt',
        ];

        // Convert snake_case parameters to camelCase
        foreach ($fieldMapping as $snakeCase => $camelCase) {
            if (isset($parameters[$snakeCase]) && !isset($parameters[$camelCase])) {
                $parameters[$camelCase] = $parameters[$snakeCase];
                unset($parameters[$snakeCase]);
            }
        }

        return parent::prepareParametersForBinding($request, $parameters, $entity, $action);
    }

    /**
     * GET /api/events/{id}/contacts
     */
    public function getContactsAction(Request $request, $id): Response
    {
        $event = $this->model->getEntity((int) $id);
        if (null === $event) {
            return $this->notFound();
        }

        if (!$this->checkEntityAccess($event, 'view')) {
            return $this->accessDenied();
        }

        $page  = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 20);
        $data  = $this->model->getAttachedContacts($event, $page, $limit);

        $view = $this->view($data, Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * GET /api/events/{id}/contacts/search?q=...
     */
    public function searchContactsAction(Request $request, $id): Response
    {
        $event = $this->model->getEntity((int) $id);
        if (null === $event) {
            return $this->notFound();
        }

        if (!$this->checkEntityAccess($event, 'view')) {
            return $this->accessDenied();
        }

        $term = (string) $request->query->get('q', '');
        if ('undefined' === $term || 'null' === $term) {
            $term = '';
        }

        $page       = $request->query->getInt('page', 1);
        $limit      = $request->query->getInt('limit', 10);
        $excludeIds = $this->doctrine->getRepository(EventContact::class)->getAttachedContactIds($event);

        $selected = $request->query->get('exclude');
        if (!is_array($selected)) {
            $selected = (null !== $selected) ? [$selected] : [];
        }
        $excludeIds = array_merge($excludeIds, array_map('intval', $selected));

        $results = $this->model->searchContacts($term, $excludeIds, $page, $limit);

        $view = $this->view(['results' => $results], Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * POST /api/events/{id}/contacts/attach
     * Body: { "contactIds": [int, ...] }
     */
    public function attachContactsAction(Request $request, $id): Response
    {
        $event = $this->model->getEntity((int) $id);
        if (null === $event) {
            return $this->notFound();
        }

        if (!$this->checkEntityAccess($event, 'edit')) {
            return $this->accessDenied();
        }

        // Accept both form and JSON payloads
        $params = $request->request->all();
        $ids    = $params['contactIds'] ?? null;
        if (null === $ids) {
            $decoded = json_decode($request->getContent(), true);
            if (is_array($decoded)) {
                $ids = $decoded['contactIds'] ?? null;
            }
        }

        if (!is_array($ids)) {
            return $this->returnError('mautic.core.error.badrequest', Response::HTTP_BAD_REQUEST, ['contactIds' => 'Invalid value']);
        }

        $this->model->attachContacts($event, array_map('intval', $ids));

        $user   = $this->getUser();
        $actorId = method_exists($user, 'getId') ? $user->getId() : null;
        foreach ($ids as $contactId) {
            $this->dispatcher->dispatch(new GenericEvent(null, [
                'eventId'   => $event->getId(),
                'contactId' => (int) $contactId,
                'actorId'   => $actorId,
                'action'    => 'attach',
            ]), 'plugin.events.contact');
        }

        $view = $this->view(['success' => true], Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * DELETE /api/events/{id}/contacts/{contactId}/detach
     */
    public function detachContactAction(Request $request, $id, $contactId): Response
    {
        $event = $this->model->getEntity((int) $id);
        if (null === $event) {
            return $this->notFound();
        }

        if (!$this->checkEntityAccess($event, 'edit')) {
            return $this->accessDenied();
        }

        $this->model->detachContact($event, (int) $contactId);

        $user   = $this->getUser();
        $actorId = method_exists($user, 'getId') ? $user->getId() : null;
        $this->dispatcher->dispatch(new GenericEvent(null, [
            'eventId'   => $event->getId(),
            'contactId' => (int) $contactId,
            'actorId'   => $actorId,
            'action'    => 'detach',
        ]), 'plugin.events.contact');

        $view = $this->view(['success' => true], Response::HTTP_OK);
        return $this->handleView($view);
    }
}
