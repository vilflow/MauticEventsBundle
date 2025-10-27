<?php

namespace MauticPlugin\MauticEventsBundle\EventListener;

use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use MauticPlugin\MauticEventsBundle\Entity\EventContactRepository;
use MauticPlugin\MauticEventsBundle\MauticEventsEvents;
use MauticPlugin\MauticEventsBundle\Form\Type\CampaignEventEventFieldValueType;
use MauticPlugin\MauticEventsBundle\Form\Type\EventDateComparisonConditionType;
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
            MauticEventsEvents::ON_CAMPAIGN_TRIGGER_DATE_COMPARISON => ['onCampaignTriggerDateComparison', 0],
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

        // Event Date Comparison Condition
        $dateComparisonCondition = [
            'label'       => 'Event Date Comparison',
            'description' => 'Compare two date fields from the event',
            'formType'    => EventDateComparisonConditionType::class,
            'formTheme'   => '@MauticEvents/FormTheme/DateComparisonCondition/_event_date_comparison_widget.html.twig',
            'eventName'   => MauticEventsEvents::ON_CAMPAIGN_TRIGGER_DATE_COMPARISON,
        ];
        $event->addCondition('events.date_comparison', $dateComparisonCondition);
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
            $operator = $config['operator'] ?? '=';
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

            try {
                $hasEvent = $this->eventContactRepository->contactHasEventByField($lead->getId(), $field, $operator, $value);
                $event->setResult($hasEvent);
                error_log(sprintf(
                    'CAMPAIGN: Event Field Value filter - Contact ID: %d, Field: %s, Operator: %s, Value: %s, Result: %s',
                    $lead->getId(),
                    $field,
                    $operator,
                    $value,
                    $hasEvent ? 'TRUE' : 'FALSE'
                ));
            } catch (\Exception $e) {
                error_log('CAMPAIGN: ' . $e->getMessage());
                error_log('CAMPAIGN: ' . $e->getTraceAsString());
                $event->setResult(false);
            }
            return;
        }

        // No matching condition found
        $event->setResult(false);
    }

    public function onCampaignTriggerDateComparison(CampaignExecutionEvent $event): void
    {
        $lead = $event->getLead();
        if (!$lead || !$lead->getId()) {
            $event->setResult(false);
            return;
        }

        $config = $event->getConfig();

        // Event Date Comparison Condition
        if ($event->checkContext('events.date_comparison')) {
            $firstDate = $config['firstDate'] ?? '';
            $operator = $config['operator'] ?? 'eq';
            $secondDate = $config['secondDate'] ?? '';

            if (empty($firstDate) || empty($secondDate)) {
                $event->setResult(false);
                return;
            }

            $result = $this->eventContactRepository->contactHasEventByDateComparison(
                $lead->getId(),
                $firstDate,
                $operator,
                $secondDate
            );

            $event->setResult($result);
            return;
        }

        // No matching condition found
        $event->setResult(false);
    }
}