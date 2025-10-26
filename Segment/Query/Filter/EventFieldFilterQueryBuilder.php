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

        // Map filter field names to actual column names
        $fieldColumn = $this->mapFilterFieldToColumn($filter->getField());

        // Check if this is a date field - if so, use DATE() function to compare only date part without time
        $isDateField = $this->isDateField($fieldColumn);

        // For date fields with comparison operators, convert parameters to date-only format first
        // This must be done BEFORE generating parameter names and holders
        $useDateOnlyComparison = $isDateField && in_array($filterOperator, ['eq', 'neq', 'gt', 'gte', 'lt', 'lte']);
        if ($useDateOnlyComparison) {
            $filterParameters = $this->convertToDateOnly($filterParameters);
        }

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

        $fieldExpression = $isDateField ? 'DATE('.$tableAlias.'_e.'.$fieldColumn.')' : $tableAlias.'_e.'.$fieldColumn;

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
                if ($useDateOnlyComparison) {
                    // Use literal value for date comparison to avoid type conversion issues
                    $dateValue = is_array($filterParameters) ? reset($filterParameters) : $filterParameters;
                    $subQueryBuilder->andWhere('DATE('.$tableAlias.'_e.'.$fieldColumn.') != '.$subQueryBuilder->expr()->literal($dateValue));
                    // Don't bind parameters for date comparisons since we're using literals
                    $parameters = null;
                } else {
                    $subQueryBuilder->andWhere($subQueryBuilder->expr()->neq($tableAlias.'_e.'.$fieldColumn, $filterParametersHolder));
                }
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
                if ($useDateOnlyComparison) {
                    // For date fields, use DATE() function to compare only the date part
                    // Use literal value to avoid type conversion issues
                    $dateValue = is_array($filterParameters) ? reset($filterParameters) : $filterParameters;
                    $subQueryBuilder->andWhere('DATE('.$tableAlias.'_e.'.$fieldColumn.') '.$this->getOperatorSymbol($filterOperator).' '.$subQueryBuilder->expr()->literal($dateValue));
                    // Don't bind parameters for date comparisons since we're using literals
                    $parameters = null;
                } else {
                    $subQueryBuilder->andWhere($subQueryBuilder->expr()->$filterOperator($tableAlias.'_e.'.$fieldColumn, $filterParametersHolder));
                }
                $queryBuilder->addLogic($queryBuilder->expr()->in($leadsTableAlias.'.id', $subQueryBuilder->getSQL()), $filter->getGlue());
                break;
            default:
                throw new \Exception('Unknown operator "'.$filterOperator.'" for event field filter');
        }

        // Only bind parameters if we're not using literals (date comparisons use literals)
        if ($parameters !== null) {
            $queryBuilder->setParametersPairs($parameters, $filterParameters);
        }

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

    /**
     * Check if a field is a date/datetime field that should be compared without time
     * This includes both DATE_MUTABLE and DATETIME_MUTABLE fields
     */
    private function isDateField(string $fieldColumn): bool
    {
        // All these fields should use DATE() comparison to ignore time component
        $dateFields = [
            'date_modified',        // DATETIME_MUTABLE
            'date_entered',         // DATETIME_MUTABLE
            'early_bird_reg_deadline_c',  // DATE_MUTABLE
            'event_start_date_c',   // DATE_MUTABLE
            'submission_deadline_c', // DATE_MUTABLE
            'event_end_date_c',     // DATE_MUTABLE
            'early_reg_deadline_c', // DATE_MUTABLE
            'final_reg_deadline_c', // DATE_MUTABLE
            'created_at',           // DATETIME_MUTABLE
            'updated_at',           // DATETIME_MUTABLE
        ];

        return in_array($fieldColumn, $dateFields);
    }

    /**
     * Convert filter operator to SQL operator symbol
     */
    private function getOperatorSymbol(string $operator): string
    {
        $operatorMap = [
            'eq' => '=',
            'neq' => '!=',
            'gt' => '>',
            'gte' => '>=',
            'lt' => '<',
            'lte' => '<=',
        ];

        return $operatorMap[$operator] ?? '=';
    }

    /**
     * Convert date parameters to date-only format (Y-m-d) if they contain time information
     * This ensures dates like '2025-10-23 10:00:01' become '2025-10-23'
     * Handles both single values and arrays
     *
     * @param mixed $filterParameters Single value or array of values
     * @return mixed Converted value(s) in the same format (single value or array)
     */
    private function convertToDateOnly($filterParameters)
    {
        if (is_array($filterParameters)) {
            return array_map(function($param) {
                return $this->extractDateFromParameter($param);
            }, $filterParameters);
        } else {
            return $this->extractDateFromParameter($filterParameters);
        }
    }

    /**
     * Extract date-only part from a parameter value
     * Handles various datetime formats including DateTime objects
     *
     * @param mixed $param Parameter value (string, DateTime, or other)
     * @return string Extracted date in Y-m-d format or original value
     */
    private function extractDateFromParameter($param)
    {
        // Handle DateTime objects
        if ($param instanceof \DateTime || $param instanceof \DateTimeInterface) {
            return $param->format('Y-m-d');
        }

        // Handle string datetime formats
        if (is_string($param) && !empty($param)) {
            // Try to match various datetime formats:
            // YYYY-MM-DD HH:MM:SS (standard MySQL datetime)
            // YYYY-MM-DD HH:MM:SS.microseconds
            // YYYY-MM-DDTHH:MM:SS (ISO 8601)
            // YYYY-MM-DDTHH:MM:SS.microseconds
            // YYYY-MM-DDTHH:MM:SS+00:00 (ISO 8601 with timezone)
            if (preg_match('/^(\d{4}-\d{2}-\d{2})[\sT]\d{2}:\d{2}/', $param, $matches)) {
                // Extract only the date part (YYYY-MM-DD)
                return $matches[1];
            }

            // Already in date-only format YYYY-MM-DD
            if (preg_match('/^(\d{4}-\d{2}-\d{2})$/', $param, $matches)) {
                return $matches[1];
            }

            // Try to parse as a date/datetime string using strtotime
            $timestamp = strtotime($param);
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }
        }

        // Return original value if we can't parse it
        return $param;
    }
}