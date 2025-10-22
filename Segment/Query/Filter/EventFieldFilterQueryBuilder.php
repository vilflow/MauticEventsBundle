<?php

namespace MauticPlugin\MauticEventsBundle\Segment\Query\Filter;

use Mautic\LeadBundle\Segment\ContactSegmentFilter;
use Mautic\LeadBundle\Segment\Query\Filter\BaseFilterQueryBuilder;
use Mautic\LeadBundle\Segment\Query\QueryBuilder;

class EventFieldFilterQueryBuilder extends BaseFilterQueryBuilder
{
    public static function getServiceId(): string
    {
        return 'mautic.events.segment.query.builder.event_field';
    }

    public function applyQuery(QueryBuilder $queryBuilder, ContactSegmentFilter $filter): QueryBuilder
    {
        $leadsTableAlias = $queryBuilder->getTableAlias(MAUTIC_TABLE_PREFIX.'leads');
        $filterOperator = $filter->getOperator();
        $filterParameters = $filter->getParameterValue();

        if (is_array($filterParameters)) {
            $parameters = [];
            foreach ($filterParameters as $filterParameter) {
                $parameters[] = $this->generateRandomParameterName();
            }
        } else {
            $parameters = $this->generateRandomParameterName();
        }

        $filterParametersHolder = $filter->getParameterHolder($parameters);
        $tableAlias = $this->generateRandomParameterName();

        // Create subquery to find contacts with matching event criteria
        $subQueryBuilder = $queryBuilder->createQueryBuilder();
        $subQueryBuilder->select($tableAlias.'_ec.contact_id')
                       ->from(MAUTIC_TABLE_PREFIX.'event_contacts', $tableAlias.'_ec')
                       ->innerJoin($tableAlias.'_ec', MAUTIC_TABLE_PREFIX.'events', $tableAlias.'_e', $tableAlias.'_ec.event_id = '.$tableAlias.'_e.id');

        // Map filter field names to actual column names
        $fieldColumn = $this->mapFilterFieldToColumn($filter->getField());

        switch ($filterOperator) {
            case 'empty':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->or(
                    $subQueryBuilder->expr()->isNull($tableAlias.'_e.'.$fieldColumn),
                    $subQueryBuilder->expr()->eq($tableAlias.'_e.'.$fieldColumn, $subQueryBuilder->expr()->literal(''))
                ));
                $queryBuilder->addLogic($queryBuilder->expr()->in($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            case 'notEmpty':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->and(
                    $subQueryBuilder->expr()->isNotNull($tableAlias.'_e.'.$fieldColumn),
                    $subQueryBuilder->expr()->neq($tableAlias.'_e.'.$fieldColumn, $subQueryBuilder->expr()->literal(''))
                ));
                $queryBuilder->addLogic($queryBuilder->expr()->in($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            case 'neq':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->neq($tableAlias.'_e.'.$fieldColumn, $filterParametersHolder));
                $queryBuilder->addLogic($queryBuilder->expr()->notIn($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            case 'notIn':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->in($tableAlias.'_e.'.$fieldColumn, $filterParametersHolder));
                $queryBuilder->addLogic($queryBuilder->expr()->notIn($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            case 'notLike':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->like($tableAlias.'_e.'.$fieldColumn, $filterParametersHolder));
                $queryBuilder->addLogic($queryBuilder->expr()->notIn($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            case 'eq':
            case 'like':
            case 'startsWith':
            case 'endsWith':
            case 'contains':
            case 'in':
            case 'gt':
            case 'gte':
            case 'lt':
            case 'lte':
            case 'regexp':
                $subQueryBuilder->andWhere($subQueryBuilder->expr()->$filterOperator($tableAlias.'_e.'.$fieldColumn, $filterParametersHolder));
                $queryBuilder->addLogic($queryBuilder->expr()->in($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            default:
                throw new \Exception('Unknown operator "'.$filterOperator.'" for event field filter');
        }

        $queryBuilder->setParametersPairs($parameters, $filterParameters);

        return $queryBuilder;
    }

    private function mapFilterFieldToColumn(string $field): string
    {
        // Map segment filter field names to actual database column names
        // Some DB columns have 'event_' prefix, others don't - this maps filter names to actual column names
        $fieldMap = [
            // Basic Event Information
            'event_name' => 'name',
            'event_description' => 'description',
            'event_about_event_c' => 'about_event_c',
            'event_external_id' => 'event_external_id',

            // Event URLs - Program and Details
            'event_program_url_c' => 'event_program_url_c',
            'event_history_url_c' => 'history_url_c',
            'event_speakers_url_c' => 'event_speakers_url_c',
            'event_submission_url_c' => 'submission_url_c',
            'event_faq_url_c' => 'event_faq_url_c',
            'event_venue_url_c' => 'event_venue_url_c',
            'event_visa_url_c' => 'visa_url_c',
            'event_registration_url_c' => 'registration_url_c',
            'event_facebook_url_c' => 'event_facebook_url_c',
            'event_feedback_url_c' => 'event_feedback_url_c',
            'event_testimonials_url_c' => 'event_testimonials_url_c',
            'event_website_url_c' => 'event_website_url_c',
            'event_easy_payment_url_c' => 'easy_payment_url_c',

            // Redirect fields
            'event_decline_redirect' => 'decline_redirect',
            'event_accept_redirect' => 'accept_redirect',

            // Event Contact Information
            'event_manager_email_c' => 'event_manager_email_c',
            'event_manager_name_c' => 'event_manager_name_c',
            'event_organizer_c' => 'event_organizer_c',

            // Event Details
            'event_isbn_number_c' => 'isbn_number_c',
            'event_wire_transfer_data_c' => 'event_wire_transfer_data_c',
            'event_duration_minutes' => 'duration_minutes',
            'event_duration_hours' => 'duration_hours',
            'event_abstract_book_image_c' => 'abstract_book_image_c',

            // Event Dates
            'event_date_modified' => 'date_modified',
            'event_date_entered' => 'date_entered',
            'event_early_bird_reg_deadline_c' => 'early_bird_reg_deadline_c',
            'event_start_date_c' => 'event_start_date_c',
            'event_submission_deadline_c' => 'submission_deadline_c',
            'event_end_date_c' => 'event_end_date_c',
            'event_early_reg_deadline_c' => 'early_reg_deadline_c',
            'event_final_reg_deadline_c' => 'final_reg_deadline_c',
            'event_created_at' => 'created_at',
            'event_updated_at' => 'updated_at',

            // Event Location
            'event_city_c' => 'event_city_c',
            'event_country_c' => 'event_country_c',
            'event_field_c' => 'event_field_c',

            // Event Financial
            'event_currency_id' => 'currency_id',
            'event_budget' => 'budget',

            // Event Status
            'event_deleted' => 'deleted',
            'event_round_c' => 'event_round_c',
            'event_activity_status_type' => 'activity_status_type',
            'event_invite_templates' => 'invite_templates',
        ];

        return $fieldMap[$field] ?? $field;
    }
}