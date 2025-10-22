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
            ->andWhere('o.deleted = 0')
            ->setParameter('contactId', $contactId, ParameterType::INTEGER);

        $column = 'e.' . $field;
        $this->applyOperatorToQuery($qb, $column, $operator, $value);

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    private function applyOperatorToQuery(QueryBuilder $qb, string $column, string $operator, mixed $value): void
    {
        $parameter = 'value';

        $trimmedValue = is_string($value) ? trim($value) : $value;

        switch ($operator) {
            case '=':
            case 'eq':
                $qb->andWhere($column.' = :'.$parameter)
                    ->setParameter($parameter, $trimmedValue);
                break;
            case '!=':
            case 'neq':
                $qb->andWhere('('.$column.' != :'.$parameter.' OR '.$column.' IS NULL)')
                    ->setParameter($parameter, $trimmedValue);
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
                $qb->andWhere($column.' = :'.$parameter)
                    ->setParameter($parameter, $trimmedValue);
                break;
            default:
                $qb->andWhere($column.' = :'.$parameter)
                    ->setParameter($parameter, $trimmedValue);
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
