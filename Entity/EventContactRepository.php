<?php

namespace MauticPlugin\MauticEventsBundle\Entity;

use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Mautic\CoreBundle\Entity\CommonRepository;
use MauticPlugin\MauticEventsBundle\Helper\EventFieldMetadataHelper;
use MauticPlugin\MauticOpportunitiesBundle\Entity\Opportunity;

/**
 * @extends CommonRepository<EventContact>
 */
class EventContactRepository extends CommonRepository
{
    private const LEGACY_FIELD_MAP = [
        'city'      => 'eventCityC',
        'country'   => 'eventCountryC',
        'currency'  => 'currencyId',
        'externalId'=> 'eventExternalId',
        'website'   => 'eventWebsiteUrlC',
    ];

    public function __construct(ManagerRegistry $registry, private EventFieldMetadataHelper $fieldMetadataHelper)
    {
        parent::__construct($registry, EventContact::class);
    }

    public function getTableAlias(): string
    {
        return 'ec';
    }

    /**
     * @return EventContact[]
     */
    public function getAttachedContacts(Event $event, int $limit, int $offset): array
    {
        $qb = $this->createQueryBuilder('ec')
            ->join('ec.contact', 'c')
            ->where('ec.event = :event')
            ->setParameter('event', $event)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->orderBy('ec.dateAdded', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function countAttachedContacts(Event $event): int
    {
        return (int) $this->createQueryBuilder('ec')
            ->select('COUNT(ec.id)')
            ->where('ec.event = :event')
            ->setParameter('event', $event)
            ->getQuery()->getSingleScalarResult();
    }

    public function getAttachedContactIds(Event $event): array
    {
        $results = $this->createQueryBuilder('ec')
            ->select('c.id')
            ->join('ec.contact', 'c')
            ->where('ec.event = :event')
            ->setParameter('event', $event)
            ->getQuery()->getScalarResult();

        return array_map('intval', array_column($results, 'id'));
    }

    public function contactHasEventByName(int $contactId, string $eventName): bool
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('1')
            ->innerJoin('ec.event', 'e')
            ->andWhere('IDENTITY(ec.contact) = :contactId')
            ->andWhere('e.name = :eventName')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('eventName', trim($eventName), ParameterType::STRING)
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasEventByCity(int $contactId, string $operator, string $city): bool
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('1')
            ->innerJoin('ec.event', 'e')
            ->andWhere('IDENTITY(ec.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('city', trim($city), ParameterType::STRING)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('e.city = :city');
                break;
            case 'neq':
                $qb->andWhere('e.city != :city OR e.city IS NULL');
                break;
            case 'like':
                $qb->andWhere('e.city LIKE :city');
                $qb->setParameter('city', '%' . trim($city) . '%', ParameterType::STRING);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasEventByCountry(int $contactId, string $operator, string $country): bool
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('1')
            ->innerJoin('ec.event', 'e')
            ->andWhere('IDENTITY(ec.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('country', trim($country), ParameterType::STRING)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('e.country = :country');
                break;
            case 'neq':
                $qb->andWhere('e.country != :country OR e.country IS NULL');
                break;
            case 'like':
                $qb->andWhere('e.country LIKE :country');
                $qb->setParameter('country', '%' . trim($country) . '%', ParameterType::STRING);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasEventByCurrency(int $contactId, string $operator, string $currency): bool
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('1')
            ->innerJoin('ec.event', 'e')
            ->andWhere('IDENTITY(ec.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('currency', trim($currency), ParameterType::STRING)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('e.currency = :currency');
                break;
            case 'neq':
                $qb->andWhere('e.currency != :currency OR e.currency IS NULL');
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasEventByExternalId(int $contactId, string $operator, string $externalId): bool
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('1')
            ->innerJoin('ec.event', 'e')
            ->andWhere('IDENTITY(ec.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('externalId', trim($externalId), ParameterType::STRING)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('e.eventExternalId = :externalId');
                break;
            case 'neq':
                $qb->andWhere('e.eventExternalId != :externalId OR e.eventExternalId IS NULL');
                break;
            case 'like':
                $qb->andWhere('e.eventExternalId LIKE :externalId');
                $qb->setParameter('externalId', '%' . trim($externalId) . '%', ParameterType::STRING);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasEventByWebsite(int $contactId, string $operator, string $website): bool
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('1')
            ->innerJoin('ec.event', 'e')
            ->andWhere('IDENTITY(ec.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('website', trim($website), ParameterType::STRING)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('e.website = :website');
                break;
            case 'neq':
                $qb->andWhere('e.website != :website OR e.website IS NULL');
                break;
            case 'like':
                $qb->andWhere('e.website LIKE :website');
                $qb->setParameter('website', '%' . trim($website) . '%', ParameterType::STRING);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasEventBySuitecrmId(int $contactId, string $operator, string $suitecrmId): bool
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('1')
            ->innerJoin('ec.event', 'e')
            ->andWhere('IDENTITY(ec.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER)
            ->setParameter('suitecrmId', trim($suitecrmId), ParameterType::STRING)
            ->setMaxResults(1);

        switch ($operator) {
            case 'eq':
                $qb->andWhere('e.suitecrmId = :suitecrmId');
                break;
            case 'neq':
                $qb->andWhere('e.suitecrmId != :suitecrmId OR e.suitecrmId IS NULL');
                break;
            case 'like':
                $qb->andWhere('e.suitecrmId LIKE :suitecrmId');
                $qb->setParameter('suitecrmId', '%' . trim($suitecrmId) . '%', ParameterType::STRING);
                break;
        }

        return $qb->getQuery()->getOneOrNullResult() !== null;
    }

    public function contactHasEventByField(int $contactId, string $field, string $operator, mixed $value): bool
    {
        $normalizedField = $this->normalizeFieldName($field);

        if (null !== $normalizedField) {
            return $this->contactHasEventByGenericField($contactId, $normalizedField, $operator, $value);
        }

        switch ($field) {
            case 'suitecrmId':
                return $this->contactHasEventBySuitecrmId($contactId, $operator, (string) $value);
            default:
                return false;
        }
    }

    private function normalizeFieldName(string $field): ?string
    {
        $field = $this->fieldMetadataHelper->sanitizeFieldAlias(trim($field));

        // Map from segment-style field names (event_xxx) to entity property names (xxxC/xxx)
        $field = $this->mapSegmentFieldToEntityProperty($field);

        if (isset(self::LEGACY_FIELD_MAP[$field])) {
            $field = self::LEGACY_FIELD_MAP[$field];
        }

        $metadata = $this->_em->getClassMetadata(Event::class);

        return $metadata->hasField($field) ? $field : null;
    }

    private function mapSegmentFieldToEntityProperty(string $field): string
    {
        // Map segment filter field names (with event_ prefix) to entity property names
        $segmentToEntityMap = [
            'event_name' => 'name',
            'event_description' => 'description',
            'event_about_event_c' => 'aboutEventC',
            'event_external_id' => 'eventExternalId',
            'event_program_url_c' => 'eventProgramUrlC',
            'event_history_url_c' => 'historyUrlC',
            'event_speakers_url_c' => 'eventSpeakersUrlC',
            'event_submission_url_c' => 'submissionUrlC',
            'event_faq_url_c' => 'eventFaqUrlC',
            'event_venue_url_c' => 'eventVenueUrlC',
            'event_visa_url_c' => 'visaUrlC',
            'event_registration_url_c' => 'registrationUrlC',
            'event_facebook_url_c' => 'eventFacebookUrlC',
            'event_feedback_url_c' => 'eventFeedbackUrlC',
            'event_testimonials_url_c' => 'eventTestimonialsUrlC',
            'event_website_url_c' => 'eventWebsiteUrlC',
            'event_easy_payment_url_c' => 'easyPaymentUrlC',
            'event_decline_redirect' => 'declineRedirect',
            'event_accept_redirect' => 'acceptRedirect',
            'event_manager_email_c' => 'eventManagerEmailC',
            'event_manager_name_c' => 'eventManagerNameC',
            'event_organizer_c' => 'eventOrganizerC',
            'event_isbn_number_c' => 'isbnNumberC',
            'event_wire_transfer_data_c' => 'eventWireTransferDataC',
            'event_duration_minutes' => 'durationMinutes',
            'event_duration_hours' => 'durationHours',
            'event_abstract_book_image_c' => 'abstractBookImageC',
            'event_date_modified' => 'dateModified',
            'event_date_entered' => 'dateEntered',
            'event_early_bird_reg_deadline_c' => 'earlyBirdRegDeadlineC',
            'event_start_date_c' => 'eventStartDateC',
            'event_submission_deadline_c' => 'submissionDeadlineC',
            'event_end_date_c' => 'eventEndDateC',
            'event_early_reg_deadline_c' => 'earlyRegDeadlineC',
            'event_final_reg_deadline_c' => 'finalRegDeadlineC',
            'event_created_at' => 'createdAt',
            'event_updated_at' => 'updatedAt',
            'event_city_c' => 'eventCityC',
            'event_country_c' => 'eventCountryC',
            'event_field_c' => 'eventFieldC',
            'event_currency_id' => 'currencyId',
            'event_budget' => 'budget',
            'event_deleted' => 'deleted',
            'event_round_c' => 'eventRoundC',
            'event_activity_status_type' => 'activityStatusType',
            'event_invite_templates' => 'inviteTemplates',
        ];

        return $segmentToEntityMap[$field] ?? $field;
    }

    private function contactHasEventByGenericField(int $contactId, string $field, string $operator, mixed $value): bool
    {
        // Check both direct event-contact relationship and via opportunities
        return $this->contactHasEventViaDirect($contactId, $field, $operator, $value) ||
               $this->contactHasEventViaOpportunity($contactId, $field, $operator, $value);
    }

    private function contactHasEventViaDirect(int $contactId, string $field, string $operator, mixed $value): bool
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('COUNT(ec.id)')
            ->innerJoin('ec.event', 'e')
            ->andWhere('IDENTITY(ec.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER);

        $column = 'e.' . $field;
        $this->applyOperatorToQuery($qb, $column, $operator, $value);

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    private function contactHasEventViaOpportunity(int $contactId, string $field, string $operator, mixed $value): bool
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('COUNT(o.id)')
            ->from(Opportunity::class, 'o')
            ->innerJoin('o.event', 'e')
            ->andWhere('o.contact = :contactId')
            ->andWhere('o.event IS NOT NULL')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER);

        // Note: We deliberately do NOT filter by o.deleted here because:
        // 1. When checking event fields (including event_deleted), we want to find ALL event relationships
        // 2. The opportunity's deleted status is independent from the event's deleted status
        // 3. Filtering by o.deleted = 0 would cause false negatives when checking event_deleted field

        $column = 'e.' . $field;
        $this->applyOperatorToQuery($qb, $column, $operator, $value);

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    private function applyOperatorToQuery(QueryBuilder $qb, string $column, string $operator, mixed $value): void
    {
        $parameter = 'value';

        $trimmedValue = is_string($value) ? trim($value) : $value;

        // Special handling for boolean fields (like 'deleted')
        // Convert string '1'/'0' to integer 1/0 for proper boolean comparison
        $isBooleanField = str_ends_with($column, '.deleted');
        if ($isBooleanField && in_array($trimmedValue, ['0', '1', 0, 1], true)) {
            $trimmedValue = (int) $trimmedValue;
        }

        switch ($operator) {
            case '=':
            case 'eq':
                $qb->andWhere($column.' = :'.$parameter);
                if ($isBooleanField) {
                    $qb->setParameter($parameter, $trimmedValue, ParameterType::INTEGER);
                } else {
                    $qb->setParameter($parameter, $trimmedValue);
                }
                break;
            case '!=':
            case 'neq':
                $qb->andWhere('('.$column.' != :'.$parameter.' OR '.$column.' IS NULL)');
                if ($isBooleanField) {
                    $qb->setParameter($parameter, $trimmedValue, ParameterType::INTEGER);
                } else {
                    $qb->setParameter($parameter, $trimmedValue);
                }
                break;
            case 'like':
                $qb->andWhere($column.' LIKE :'.$parameter)
                    ->setParameter($parameter, '%'.$trimmedValue.'%');
                break;
            case '!like':
                $qb->andWhere($column.' NOT LIKE :'.$parameter)
                    ->setParameter($parameter, '%'.$trimmedValue.'%');
                break;
            case 'contains':
                $qb->andWhere($column.' LIKE :'.$parameter)
                    ->setParameter($parameter, '%'.$trimmedValue.'%');
                break;
            case 'startsWith':
                $qb->andWhere($column.' LIKE :'.$parameter)
                    ->setParameter($parameter, $trimmedValue.'%');
                break;
            case 'endsWith':
                $qb->andWhere($column.' LIKE :'.$parameter)
                    ->setParameter($parameter, '%'.$trimmedValue);
                break;
            case 'gt':
                $qb->andWhere($column.' > :'.$parameter)
                    ->setParameter($parameter, $trimmedValue);
                break;
            case 'gte':
                $qb->andWhere($column.' >= :'.$parameter)
                    ->setParameter($parameter, $trimmedValue);
                break;
            case 'lt':
                $qb->andWhere($column.' < :'.$parameter)
                    ->setParameter($parameter, $trimmedValue);
                break;
            case 'lte':
                $qb->andWhere($column.' <= :'.$parameter)
                    ->setParameter($parameter, $trimmedValue);
                break;
            case 'in':
                $values = is_array($trimmedValue) ? $trimmedValue : array_map('trim', explode(',', (string) $trimmedValue));
                $values = array_values(array_filter($values, static fn($item) => $item !== '' && $item !== null));
                if (empty($values)) {
                    $qb->andWhere('1 = 0');
                    break;
                }
                $qb->andWhere($column.' IN (:'.$parameter.')')
                    ->setParameter($parameter, $values);
                break;
            case '!in':
                $values = is_array($trimmedValue) ? $trimmedValue : array_map('trim', explode(',', (string) $trimmedValue));
                $values = array_values(array_filter($values, static fn($item) => $item !== '' && $item !== null));
                if (empty($values)) {
                    break;
                }
                $qb->andWhere($column.' NOT IN (:'.$parameter.')')
                    ->setParameter($parameter, $values);
                break;
            case 'empty':
                $qb->andWhere('('.$column.' IS NULL OR '.$column." = '' )");
                break;
            case '!empty':
                $qb->andWhere($column.' IS NOT NULL')
                    ->andWhere($column." != ''");
                break;
            case 'regexp':
                $qb->andWhere($column.' REGEXP :'.$parameter)
                    ->setParameter($parameter, $trimmedValue);
                break;
            case '!regexp':
                $qb->andWhere($column.' NOT REGEXP :'.$parameter)
                    ->setParameter($parameter, $trimmedValue);
                break;
            case 'date':
                // Handle anniversary special case - match month and day only
                if ('anniversary' === $trimmedValue) {
                    // For anniversary, extract month and day and compare
                    $dateValue = $this->convertRelativeDateToActual($trimmedValue);
                    $qb->andWhere('SUBSTRING('.$column.', 6, 5) = SUBSTRING(:'.$parameter.', 6, 5)')
                        ->setParameter($parameter, $dateValue);
                } else {
                    // Check if this is a relative interval (e.g., -30, +30, -P50D, +P1M)
                    $isRelativeInterval = $this->isRelativeInterval($trimmedValue);

                    if ($isRelativeInterval) {
                        // For relative intervals: create a RANGE filter
                        // -30 means: between (today - 30 days) and today
                        // +30 means: between today and (today + 30 days)
                        $rangeParams = $this->calculateDateRange($trimmedValue);
                        $startDate = $rangeParams['start'];
                        $endDate = $rangeParams['end'];

                        // Get field metadata to check if it's a datetime or date field
                        $metadata = $this->_em->getClassMetadata(Event::class);
                        $fieldName = str_replace('e.', '', $column);

                        $useSubstring = false;
                        if ($metadata->hasField($fieldName)) {
                            $fieldMapping = $metadata->getFieldMapping($fieldName);
                            $fieldType = $fieldMapping['type'] ?? 'string';
                            $useSubstring = in_array($fieldType, ['datetime', 'datetimetz', 'datetime_immutable']);
                        }

                        // Build BETWEEN query
                        if ($useSubstring) {
                            // For datetime fields, extract date part first
                            $qb->andWhere('SUBSTRING('.$column.', 1, 10) BETWEEN :startDate AND :endDate')
                                ->setParameter('startDate', $startDate)
                                ->setParameter('endDate', $endDate);
                        } else {
                            // For date fields, direct comparison
                            $qb->andWhere($column.' BETWEEN :startDate AND :endDate')
                                ->setParameter('startDate', $startDate)
                                ->setParameter('endDate', $endDate);
                        }
                    } else {
                        // For absolute dates (e.g., "2025-10-27", "today", "yesterday"): exact match
                        $dateValue = $this->convertRelativeDateToActual($trimmedValue);

                        // Get field metadata to check if it's a datetime or date field
                        $metadata = $this->_em->getClassMetadata(Event::class);
                        $fieldName = str_replace('e.', '', $column);

                        if ($metadata->hasField($fieldName)) {
                            $fieldMapping = $metadata->getFieldMapping($fieldName);
                            $fieldType = $fieldMapping['type'] ?? 'string';

                            // For datetime fields, we need to extract just the date part
                            if (in_array($fieldType, ['datetime', 'datetimetz', 'datetime_immutable'])) {
                                // Use SUBSTRING to extract YYYY-MM-DD from YYYY-MM-DD HH:MM:SS
                                $qb->andWhere('SUBSTRING('.$column.', 1, 10) = :'.$parameter)
                                    ->setParameter($parameter, $dateValue);
                            } else {
                                // For date fields, direct comparison works
                                $qb->andWhere($column.' = :'.$parameter)
                                    ->setParameter($parameter, $dateValue);
                            }
                        } else {
                            // Fallback: assume datetime and extract date part
                            $qb->andWhere('SUBSTRING('.$column.', 1, 10) = :'.$parameter)
                                ->setParameter($parameter, $dateValue);
                        }
                    }
                }
                break;
            default:
                $qb->andWhere($column.' = :'.$parameter)
                    ->setParameter($parameter, $trimmedValue);
        }
    }

    /**
     * Check if value is a relative interval that should create a date range
     * Returns true for values like: -30, +30, -P50D, +P1M (not for +P0D, -P1D, +P1D which are today/yesterday/tomorrow)
     */
    private function isRelativeInterval(string $value): bool
    {
        // Check for ISO 8601 duration format with +/- prefix
        if (preg_match('/^([+-])(PT?)(\d+)([DIMHWY])$/i', $value, $matches)) {
            $amount = (int)$matches[3];

            // Special cases that are exact dates, not ranges:
            // +P0D = today, -P1D = yesterday, +P1D = tomorrow
            if ($amount === 0) {
                return false; // +P0D = today (exact date)
            }
            if ($amount === 1 && strtoupper($matches[4]) === 'D') {
                return false; // -P1D = yesterday, +P1D = tomorrow (exact dates)
            }

            return true; // Everything else is a range
        }

        return false; // Non-interval values (today, yesterday, 2025-10-27, etc.) are exact dates
    }

    /**
     * Calculate date range for relative interval
     * For negative values (-30): returns range from (today - interval) to today
     * For positive values (+30): returns range from today to (today + interval)
     */
    private function calculateDateRange(string $value): array
    {
        $today = new \DateTime('now', new \DateTimeZone('UTC'));
        $today->setTime(0, 0, 0); // Reset to start of day

        // Parse the interval
        if (preg_match('/^([+-])(PT?)(\d+)([DIMHWY])$/i', $value, $matches)) {
            $sign = $matches[1];
            $timePrefix = strtoupper($matches[2]);
            $amount = $matches[3];
            $unit = strtoupper($matches[4]);

            // For time-based intervals (PT prefix with H or M), we need different handling
            $isTimeInterval = ($timePrefix === 'PT' && in_array($unit, ['H', 'M']));

            // Convert unit to DateTime modifier format
            $unitMap = [
                'D' => 'day',
                'W' => 'week',
                'M' => $isTimeInterval ? 'minute' : 'month',
                'Y' => 'year',
                'H' => 'hour',
                'I' => 'minute',
            ];

            $modifier = $amount . ' ' . ($unitMap[$unit] ?? 'day');
            if ((int)$amount !== 1) {
                $modifier .= 's';
            }

            if ($sign === '-') {
                // Negative: from (today - interval) to today
                // Example: -30 days means from 30 days ago to today
                $startDate = clone $today;
                $startDate->modify('-' . $modifier);
                $endDate = clone $today;

                return [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                ];
            } else {
                // Positive: from today to (today + interval)
                // Example: +30 days means from today to 30 days from now
                $startDate = clone $today;
                $endDate = clone $today;
                $endDate->modify('+' . $modifier);

                return [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d'),
                ];
            }
        }

        // Fallback: return today to today (exact match)
        return [
            'start' => $today->format('Y-m-d'),
            'end' => $today->format('Y-m-d'),
        ];
    }

    /**
     * Convert relative date values to actual dates
     * Handles values like '+P0D' (today), '-P1D' (yesterday), '+P50D' (-50 days), 'anniversary'
     * Also handles time intervals: '-PT5H' (-5 hours), '+PT30M' (+30 minutes)
     */
    private function convertRelativeDateToActual(string $value): string
    {
        // Handle ISO 8601 duration format with +/- prefix
        // Matches: +P50D, -P1D, +PT5H, -PT30M, +P2W, +P3M, +P1Y
        if (preg_match('/^([+-])(PT?)(\d+)([DIMHWY])$/i', $value, $matches)) {
            $sign = $matches[1];
            $timePrefix = strtoupper($matches[2]); // 'PT' for time intervals (hours, minutes), 'P' for date intervals
            $amount = $matches[3];
            $unit = strtoupper($matches[4]);

            // For time-based intervals (PT prefix with H or M), we need different handling
            $isTimeInterval = ($timePrefix === 'PT' && in_array($unit, ['H', 'M']));

            // Convert unit to DateTime modifier format
            $unitMap = [
                'D' => 'day',
                'W' => 'week',
                'M' => $isTimeInterval ? 'minute' : 'month', // M after PT = minute, M after P = month
                'Y' => 'year',
                'H' => 'hour',
                'I' => 'minute', // Alternative notation for minutes
            ];

            $modifier = $sign . $amount . ' ' . ($unitMap[$unit] ?? 'day');
            if ((int)$amount !== 1) {
                $modifier .= 's';
            }

            $date = new \DateTime('now', new \DateTimeZone('UTC'));
            $date->modify($modifier);

            return $date->format('Y-m-d');
        }

        // Handle special keywords
        switch (strtolower($value)) {
            case 'today':
                return (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d');
            case 'yesterday':
                return (new \DateTime('yesterday', new \DateTimeZone('UTC')))->format('Y-m-d');
            case 'tomorrow':
                return (new \DateTime('tomorrow', new \DateTimeZone('UTC')))->format('Y-m-d');
            case 'anniversary':
                // For anniversary, return today's date (month-day matching will be done in the query)
                return (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d');
            default:
                // If it's already a valid date format, return as-is
                // Otherwise try to parse it
                try {
                    $date = new \DateTime($value, new \DateTimeZone('UTC'));
                    return $date->format('Y-m-d');
                } catch (\Exception $e) {
                    // If parsing fails, return original value
                    return $value;
                }
        }
    }

    public function contactHasEventByDateComparison(int $contactId, string $firstDateField, string $operator, string $secondDateField): bool
    {
        // Normalize field names
        $normalizedFirstDate = $this->normalizeFieldName($firstDateField);
        $normalizedSecondDate = $this->normalizeFieldName($secondDateField);

        if (null === $normalizedFirstDate || null === $normalizedSecondDate) {
            return false;
        }

        // Check both direct event-contact relationship and via opportunities
        return $this->contactHasEventByDateComparisonDirect($contactId, $normalizedFirstDate, $operator, $normalizedSecondDate) ||
               $this->contactHasEventByDateComparisonViaOpportunity($contactId, $normalizedFirstDate, $operator, $normalizedSecondDate);
    }

    private function contactHasEventByDateComparisonDirect(int $contactId, string $normalizedFirstDate, string $operator, string $normalizedSecondDate): bool
    {
        $qb = $this->createQueryBuilder('ec')
            ->select('COUNT(ec.id)')
            ->innerJoin('ec.event', 'e')
            ->andWhere('IDENTITY(ec.contact) = :contactId')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER);

        // Build the comparison condition
        $firstColumn = 'e.' . $normalizedFirstDate;
        $secondColumn = 'e.' . $normalizedSecondDate;

        // Add null checks to ensure both dates exist
        $qb->andWhere($firstColumn . ' IS NOT NULL')
            ->andWhere($secondColumn . ' IS NOT NULL');

        // Apply the operator
        $this->applyDateComparisonOperator($qb, $firstColumn, $secondColumn, $operator);

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    private function contactHasEventByDateComparisonViaOpportunity(int $contactId, string $normalizedFirstDate, string $operator, string $normalizedSecondDate): bool
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('COUNT(o.id)')
            ->from(Opportunity::class, 'o')
            ->innerJoin('o.event', 'e')
            ->andWhere('o.contact = :contactId')
            ->andWhere('o.event IS NOT NULL')
            ->andWhere('o.deleted = 0')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER);

        // Build the comparison condition
        $firstColumn = 'e.' . $normalizedFirstDate;
        $secondColumn = 'e.' . $normalizedSecondDate;

        // Add null checks to ensure both dates exist
        $qb->andWhere($firstColumn . ' IS NOT NULL')
            ->andWhere($secondColumn . ' IS NOT NULL');

        // Apply the operator
        $this->applyDateComparisonOperator($qb, $firstColumn, $secondColumn, $operator);

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    private function applyDateComparisonOperator(QueryBuilder $qb, string $firstColumn, string $secondColumn, string $operator): void
    {
        switch ($operator) {
            case 'lt':
                $qb->andWhere($firstColumn . ' < ' . $secondColumn);
                break;
            case 'lte':
                $qb->andWhere($firstColumn . ' <= ' . $secondColumn);
                break;
            case 'gt':
                $qb->andWhere($firstColumn . ' > ' . $secondColumn);
                break;
            case 'gte':
                $qb->andWhere($firstColumn . ' >= ' . $secondColumn);
                break;
            case '=':
            case 'eq':
                $qb->andWhere($firstColumn . ' = ' . $secondColumn);
                break;
            case '!=':
            case 'neq':
                $qb->andWhere($firstColumn . ' != ' . $secondColumn);
                break;
        }
    }
}
