<?php

namespace MauticPlugin\MauticEventsBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\LeadBundle\Event\LeadListFiltersChoicesEvent;
use Mautic\LeadBundle\Event\SegmentDictionaryGenerationEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\LeadBundle\Provider\TypeOperatorProviderInterface;
use Mautic\LeadBundle\Segment\Query\Filter\ForeignValueFilterQueryBuilder;
use MauticPlugin\MauticEventsBundle\Segment\Query\Filter\EventFieldFilterQueryBuilder;
use MauticPlugin\MauticEventsBundle\Entity\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SegmentFilterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TypeOperatorProviderInterface $typeOperatorProvider,
        private TranslatorInterface $translator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LeadEvents::LIST_FILTERS_CHOICES_ON_GENERATE => [
                ['onGenerateSegmentFiltersAddEventFields', -10],
            ],
            LeadEvents::SEGMENT_DICTIONARY_ON_GENERATE => [
                ['onSegmentDictionaryGenerate', 0],
            ],
        ];
    }

    public function onGenerateSegmentFiltersAddEventFields(LeadListFiltersChoicesEvent $event): void
    {
        if (!$event->isForSegmentation()) {
            return;
        }

        $choices = [
            // Basic Event Information
            'event_external_id' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_external_id'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_name' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_name'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_description' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_description'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_about_event_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_about_event_c'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],

            // Event URLs
            'event_program_url_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_program_url_c'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_history_url_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_history_url_c'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_speakers_url_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_speakers_url_c'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_submission_url_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_submission_url_c'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_faq_url_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_faq_url_c'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_venue_url_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_venue_url_c'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_visa_url_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_visa_url_c'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_registration_url_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_registration_url_c'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_facebook_url_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_facebook_url_c'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_feedback_url_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_feedback_url_c'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_testimonials_url_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_testimonials_url_c'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_website_url_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_website_url_c') . ' (Legacy)',
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_easy_payment_url_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_easy_payment_url_c'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],

            // Event Redirect URLs
            'event_decline_redirect' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_decline_redirect'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_accept_redirect' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_accept_redirect'),
                'properties' => ['type' => 'url'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],

            // Event Contact Information
            'event_manager_email_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_manager_email_c'),
                'properties' => ['type' => 'email'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('email'),
                'object'     => 'lead',
            ],
            'event_manager_name_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_manager_name_c'),
                'properties' => [
                    'type' => 'select',
                    'list' => [
                        'Romina Dellucci' => 'Romina_Dellucci',
                        'Laura Johnson' => 'Laura_Johnson',
                    ],
                ],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('select'),
                'object'     => 'lead',
            ],
            'event_organizer_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_organizer_c'),
                'properties' => [
                    'type' => 'select',
                    'list' => [
                        'Acavent' => 'Acavent',
                        'GlobalKS' => 'GlobalKS',
                        'STE' => 'STE',
                        'ProudPen' => 'ProudPen',
                    ],
                ],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('select'),
                'object'     => 'lead',
            ],

            // Event Details
            'event_isbn_number_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_isbn_number_c'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_wire_transfer_data_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_wire_transfer_data_c'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_duration_minutes' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_duration_minutes'),
                'properties' => ['type' => 'number'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('number'),
                'object'     => 'lead',
            ],
            'event_duration_hours' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_duration_hours'),
                'properties' => ['type' => 'number'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('number'),
                'object'     => 'lead',
            ],
            'event_abstract_book_image_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_abstract_book_image_c'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],

            // Event Dates
            'event_date_modified' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_date_modified'),
                'properties' => ['type' => 'datetime'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('default'),
                'object'     => 'lead',
            ],
            'event_date_entered' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_date_entered'),
                'properties' => ['type' => 'datetime'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('default'),
                'object'     => 'lead',
            ],
            'event_early_bird_reg_deadline_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_early_bird_reg_deadline_c'),
                'properties' => ['type' => 'datetime'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('default'),
                'object'     => 'lead',
            ],
            'event_start_date_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_start_date_c'),
                'properties' => ['type' => 'datetime'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('default'),
                'object'     => 'lead',
            ],
            'event_submission_deadline_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_submission_deadline_c'),
                'properties' => ['type' => 'datetime'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('default'),
                'object'     => 'lead',
            ],
            'event_end_date_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_end_date_c'),
                'properties' => ['type' => 'datetime'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('default'),
                'object'     => 'lead',
            ],
            'event_early_reg_deadline_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_early_reg_deadline_c'),
                'properties' => ['type' => 'datetime'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('default'),
                'object'     => 'lead',
            ],
            'event_final_reg_deadline_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_final_reg_deadline_c'),
                'properties' => ['type' => 'datetime'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('default'),
                'object'     => 'lead',
            ],
            'event_created_at' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_created_at'),
                'properties' => ['type' => 'datetime'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('default'),
                'object'     => 'lead',
            ],
            'event_updated_at' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_updated_at'),
                'properties' => ['type' => 'datetime'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('default'),
                'object'     => 'lead',
            ],

            // Event Location
            'event_city_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_city_c'),
                'properties' => [
                    'type' => 'select',
                    'list' => [
                        'Geneva' => 'Geneva',
                        'Prague' => 'Prague',
                        'Copenhagen' => 'Copenhagen',
                        'Berlin' => 'Berlin',
                        'Vienna' => 'Vienna',
                        'Lisbon' => 'Lisbon',
                    ],
                ],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('select'),
                'object'     => 'lead',
            ],
            'event_country_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_country_c'),
                'properties' => [
                    'type' => 'select',
                    'list' => [
                        'Switzerland' => 'Switzerland',
                        'Czech Republic' => 'Czech_Republic',
                        'Denmark' => 'Denmark',
                        'Germany' => 'Germany',
                        'Austria' => 'Austria',
                        'Portugal' => 'Portugal',
                    ],
                ],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('select'),
                'object'     => 'lead',
            ],
            'event_field_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_field_c'),
                'properties' => [
                    'type' => 'select',
                    'list' => [
                        'Education' => 'Education',
                        'Social Sciences' => 'Social_Sciences',
                        'Management' => 'Management',
                    ],
                ],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('select'),
                'object'     => 'lead',
            ],

            // Event Financial
            'event_currency_id' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_currency_id'),
                'properties' => ['type' => 'text'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('text'),
                'object'     => 'lead',
            ],
            'event_budget' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_budget'),
                'properties' => ['type' => 'number'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('number'),
                'object'     => 'lead',
            ],

            // Event Status
            'event_deleted' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_deleted'),
                'properties' => ['type' => 'boolean'],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('boolean'),
                'object'     => 'lead',
            ],
            'event_round_c' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_round_c'),
                'properties' => [
                    'type' => 'select',
                    'list' => [
                        '1st' => '1st',
                        '2nd' => '2nd',
                        '3rd' => '3rd',
                        '4th' => '4th',
                        '5th' => '5th',
                    ],
                ],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('select'),
                'object'     => 'lead',
            ],
            'event_activity_status_type' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_activity_status_type'),
                'properties' => [
                    'type' => 'select',
                    'list' => [
                        'Active' => 'active',
                        'Inactive' => 'inactive',
                    ],
                ],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('select'),
                'object'     => 'lead',
            ],
            'event_invite_templates' => [
                'label'      => $this->translator->trans('mautic.events.segment.event_invite_templates'),
                'properties' => [
                    'type' => 'select',
                    'list' => [
                        'Campaign' => 'campaign',
                        'Email' => 'email',
                        'Event' => 'event',
                    ],
                ],
                'operators'  => $this->typeOperatorProvider->getOperatorsForFieldType('select'),
                'object'     => 'lead',
            ],
        ];

        foreach ($choices as $alias => $fieldOptions) {
            $event->addChoice('Event', $alias, $fieldOptions);
        }
    }

    public function onSegmentDictionaryGenerate(SegmentDictionaryGenerationEvent $event): void
    {
        // Use custom EventFieldFilterQueryBuilder for event segment filters
        $eventFields = [
            'event_external_id', 'event_name', 'event_description', 'event_about_event_c',
            'event_program_url_c', 'event_history_url_c', 'event_speakers_url_c', 'event_submission_url_c',
            'event_faq_url_c', 'event_venue_url_c', 'event_visa_url_c', 'event_registration_url_c',
            'event_facebook_url_c', 'event_feedback_url_c', 'event_testimonials_url_c',
            'event_website_url_c',  // Legacy field name support
            'event_easy_payment_url_c', 'event_decline_redirect', 'event_accept_redirect',
            'event_manager_email_c', 'event_manager_name_c', 'event_organizer_c',
            'event_isbn_number_c', 'event_wire_transfer_data_c', 'event_duration_minutes', 'event_duration_hours',
            'event_abstract_book_image_c', 'event_date_modified', 'event_date_entered',
            'event_early_bird_reg_deadline_c', 'event_start_date_c', 'event_submission_deadline_c',
            'event_end_date_c', 'event_early_reg_deadline_c', 'event_final_reg_deadline_c',
            'event_created_at', 'event_updated_at', 'event_city_c', 'event_country_c', 'event_field_c',
            'event_currency_id', 'event_budget', 'event_deleted', 'event_round_c',
            'event_activity_status_type', 'event_invite_templates'
        ];

        foreach ($eventFields as $fieldName) {
            $event->addTranslation($fieldName, [
                'type' => EventFieldFilterQueryBuilder::getServiceId(),
                'field' => $fieldName,
            ]);
        }
    }
}