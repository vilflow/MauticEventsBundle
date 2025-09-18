<?php

namespace MauticPlugin\MauticEventsBundle\Helper;

use Mautic\CoreBundle\Translation\Translator;
use Mautic\LeadBundle\Helper\FormFieldHelper;

class EventFieldMetadataHelper
{
    private const SELECT_FIELDS = [
        'eventOrganizerC',
        'eventRoundC',
        'eventManagerNameC',
        'eventCityC',
        'eventFieldC',
        'eventCountryC',
        'activityStatusType',
        'inviteTemplates',
        'deleted',
    ];

    private const NUMBER_FIELDS = [
        'durationMinutes',
        'durationHours',
        'budget',
    ];

    private const DATE_FIELDS = [
        'dateModified',
        'dateEntered',
        'earlyBirdRegDeadlineC',
        'eventStartDateC',
        'submissionDeadlineC',
        'eventEndDateC',
        'earlyRegDeadlineC',
        'finalRegDeadlineC',
        'createdAt',
        'updatedAt',
    ];

    public function __construct(private Translator $translator)
    {
    }

    public function getFieldType(?string $field): string
    {
        if (null === $field) {
            return 'default';
        }

        if (in_array($field, self::SELECT_FIELDS, true)) {
            return 'deleted' === $field ? 'bool' : 'select';
        }

        if (in_array($field, self::NUMBER_FIELDS, true)) {
            return 'number';
        }

        if (in_array($field, self::DATE_FIELDS, true)) {
            return 'date';
        }

        return 'text';
    }

    /**
     * @return array{options:?array<string,string>, customChoiceValue:?string, optionsAttr:array<string,array<string,mixed>>}
     */
    public function getFieldOptions(string $field, string $operator, mixed $currentValue = null): array
    {
        $options = null;
        $customChoiceValue = null;
        $optionsAttr = [];

        switch ($field) {
            case 'eventOrganizerC':
                $options = [
                    'Acavent'   => 'Acavent',
                    'GlobalKS'  => 'GlobalKS',
                    'STE'       => 'STE',
                    'ProudPen'  => 'ProudPen',
                ];
                break;
            case 'eventRoundC':
                $options = [
                    '1st' => '1st',
                    '2nd' => '2nd',
                    '3rd' => '3rd',
                    '4th' => '4th',
                    '5th' => '5th',
                ];
                break;
            case 'eventManagerNameC':
                $options = [
                    'Romina_Dellucci' => 'Romina Dellucci',
                    'Laura_Johnson'   => 'Laura Johnson',
                ];
                break;
            case 'eventCityC':
                $options = [
                    'Geneva'     => 'Geneva',
                    'Prague'     => 'Prague',
                    'Copenhagen' => 'Copenhagen',
                    'Berlin'     => 'Berlin',
                    'Vienna'     => 'Vienna',
                    'Lisbon'     => 'Lisbon',
                ];
                break;
            case 'eventFieldC':
                $options = [
                    'Education'       => 'Education',
                    'Social_Sciences' => 'Social Sciences',
                    'Management'      => 'Management',
                ];
                break;
            case 'eventCountryC':
                $options = [
                    'Switzerland'    => 'Switzerland',
                    'Czech_Republic' => 'Czech Republic',
                    'Denmark'        => 'Denmark',
                    'Germany'        => 'Germany',
                    'Austria'        => 'Austria',
                    'Portugal'       => 'Portugal',
                ];
                break;
            case 'activityStatusType':
                $options = [
                    'active'   => 'Active',
                    'inactive' => 'Inactive',
                ];
                break;
            case 'inviteTemplates':
                $options = [
                    'campaign' => 'Campaign',
                    'email'    => 'Email',
                    'event'    => 'Event',
                ];
                break;
            case 'deleted':
                $options = [
                    '0' => $this->translator->trans('mautic.core.no'),
                    '1' => $this->translator->trans('mautic.core.yes'),
                ];
                break;
            default:
                if (in_array($field, self::DATE_FIELDS, true) && 'date' === $operator) {
                    $fieldHelper = new FormFieldHelper();
                    $fieldHelper->setTranslator($this->translator);
                    $dateChoices = $fieldHelper->getDateChoices();
                    $customChoiceValue = (empty($currentValue) || isset($dateChoices[$currentValue])) ? 'custom' : (string) $currentValue;
                    $options = [$customChoiceValue => $this->translator->trans('mautic.campaign.event.timed.choice.custom')] + $dateChoices;
                    $optionsAttr[$customChoiceValue] = ['data-custom' => 1];
                }
                break;
        }

        return [
            'options'           => $options,
            'customChoiceValue' => $customChoiceValue,
            'optionsAttr'       => $optionsAttr,
        ];
    }
}
