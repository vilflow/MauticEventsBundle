<?php

namespace MauticPlugin\MauticEventsBundle\EventListener;

use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use MauticPlugin\MauticEventsBundle\Entity\EventContactRepository;
use MauticPlugin\MauticEventsBundle\MauticEventsEvents;
use MauticPlugin\MauticEventsBundle\Form\Type\CampaignEventEventFieldValueType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EventContactRepository $eventContactRepository
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD         => ['onCampaignBuild', 0],
            MauticEventsEvents::ON_CAMPAIGN_TRIGGER_CONDITION => ['onCampaignTriggerCondition', 0],
        ];
    }

    public function onCampaignBuild(CampaignBuilderEvent $event): void
    {
        // Event Field Value Condition
        $condition = [
            'label'       => 'mautic.events.campaign.condition.event_field_value',
            'description' => 'mautic.events.campaign.condition.event_field_value_descr',
            'formType'    => CampaignEventEventFieldValueType::class,
            'formTheme'   => '@MauticEvents/FormTheme/FieldValueCondition/_campaignevent_event_field_value_widget.html.twig',
            'eventName'   => MauticEventsEvents::ON_CAMPAIGN_TRIGGER_CONDITION,
        ];
        $event->addCondition('events.field_value', $condition);
    }

    public function onCampaignTriggerCondition(CampaignExecutionEvent $event): void
    {
        $lead = $event->getLead();
        if (!$lead || !$lead->getId()) {
            $event->setResult(false);
            return;
        }

        $config = $event->getConfig();

        // Event Field Value Condition
        if ($event->checkContext('events.field_value')) {
            $field = $config['field'] ?? '';
            $operator = $config['operator'] ?? 'eq';
            $value = $config['value'] ?? '';

            if (empty($field)) {
                $event->setResult(false);
                return;
            }

            // Check if value is required for this operator
            $operatorsWithoutValue = ['empty', '!empty'];
            if (!in_array($operator, $operatorsWithoutValue) && empty($value)) {
                $event->setResult(false);
                return;
            }

            $hasEvent = $this->eventContactRepository->contactHasEventByField($lead->getId(), $field, $operator, $value);
            $event->setResult($hasEvent);
            return;
        }

        // No matching condition found
        $event->setResult(false);
    }
}