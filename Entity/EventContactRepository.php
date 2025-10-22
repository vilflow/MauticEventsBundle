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

        if (isset(self::LEGACY_FIELD_MAP[$field])) {
            $field = self::LEGACY_FIELD_MAP[$field];
        }

        $metadata = $this->_em->getClassMetadata(Event::class);

        return $metadata->hasField($field) ? $field : null;
    }

    private function contactHasEventByGenericField(int $contactId, string $field, string $operator, mixed $value): bool
    {
        // Check via opportunities only. This ensures the event is linked to one of the contact's opportunities.
        return $this->contactHasEventViaOpportunity($contactId, $field, $operator, $value);
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
            case 'eq':
                $qb->andWhere($column.' = :'.$parameter)
                    ->setParameter($parameter, $trimmedValue);
                break;
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

        // Query via opportunities to ensure proper relationship
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
            case 'eq':
                $qb->andWhere($firstColumn . ' = ' . $secondColumn);
                break;
            case 'neq':
                $qb->andWhere($firstColumn . ' != ' . $secondColumn);
                break;
            default:
                return false;
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }
}
