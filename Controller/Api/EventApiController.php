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
        if (isset($parameters['event_external_id']) && !isset($parameters['eventExternalId'])) {
            $parameters['eventExternalId'] = $parameters['event_external_id'];
            unset($parameters['event_external_id']);
        }
        if (isset($parameters['registration_url']) && !isset($parameters['registrationUrl'])) {
            $parameters['registrationUrl'] = $parameters['registration_url'];
            unset($parameters['registration_url']);
        }
        if (isset($parameters['suitecrm_id']) && !isset($parameters['suitecrmId'])) {
            $parameters['suitecrmId'] = $parameters['suitecrm_id'];
            unset($parameters['suitecrm_id']);
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
