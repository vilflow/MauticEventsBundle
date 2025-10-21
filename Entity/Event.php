<?php

namespace MauticPlugin\MauticEventsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\ApiBundle\Serializer\Driver\ApiMetadataDriver;
use Mautic\CoreBundle\Entity\CommonEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use MauticPlugin\MauticEventsBundle\Entity\EventContact;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * @ORM\Entity
 */
class Event extends CommonEntity
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $eventExternalId;

    /**
     * @var string|null
     */
    private $eventProgramUrlC;

    /**
     * @var string|null
     */
    private $historyUrlC;

    /**
     * @var string|null
     */
    private $eventSpeakersUrlC;

    /**
     * @var string|null
     */
    private $submissionUrlC;

    /**
     * @var string|null
     */
    private $eventFaqUrlC;

    /**
     * @var string|null
     */
    private $eventVenueUrlC;

    /**
     * @var string|null
     */
    private $visaUrlC;

    /**
     * @var string|null
     */
    private $registrationUrlC;

    /**
     * @var string|null
     */
    private $eventFacebookUrlC;

    /**
     * @var string|null
     */
    private $eventFeedbackUrlC;

    /**
     * @var string|null
     */
    private $eventTestimonialsUrlC;

    /**
     * @var string|null
     */
    private $eventWebsiteUrlC;

    /**
     * @var string|null
     */
    private $easyPaymentUrlC;

    /**
     * @var string|null
     */
    private $declineRedirect;

    /**
     * @var string|null
     */
    private $acceptRedirect;

    /**
     * @var string|null
     */
    private $eventManagerEmailC;

    /**
     * @var string|null
     */
    private $isbnNumberC;

    /**
     * @var string|null
     */
    private $eventWireTransferDataC;

    /**
     * @var string|null
     */
    private $aboutEventC;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var int|null
     */
    private $durationMinutes;

    /**
     * @var int|null
     */
    private $durationHours;

    /**
     * @var string|null
     */
    private $abstractBookImageC;


    /**
     * @var \DateTime|null
     */
    private $dateModified;

    /**
     * @var \DateTime|null
     */
    private $dateEntered;

    /**
     * @var \DateTime|null
     */
    private $earlyBirdRegDeadlineC;

    /**
     * @var \DateTime|null
     */
    private $eventStartDateC;

    /**
     * @var \DateTime|null
     */
    private $submissionDeadlineC;

    /**
     * @var \DateTime|null
     */
    private $eventEndDateC;

    /**
     * @var \DateTime|null
     */
    private $earlyRegDeadlineC;

    /**
     * @var \DateTime|null
     */
    private $finalRegDeadlineC;

    /**
     * @var string|null
     */
    private $currencyId;

    /**
     * @var float|null
     */
    private $budget;

    /**
     * @var bool
     */
    private $deleted = false;

    /**
     * @var string|null
     */
    private $eventOrganizerC;

    /**
     * @var string|null
     */
    private $eventRoundC;

    /**
     * @var string|null
     */
    private $eventManagerNameC;

    /**
     * @var string|null
     */
    private $eventCityC;

    /**
     * @var string|null
     */
    private $eventFieldC;

    /**
     * @var string|null
     */
    private $eventCountryC;

    /**
     * @var string|null
     */
    private $activityStatusType;

    /**
     * @var string|null
     */
    private $inviteTemplates;

    /**
     * @var \DateTime|null
     */
    private $createdAt;

    /**
     * @var \DateTime|null
     */
    private $updatedAt;

    /**
     * @var Collection<int, EventContact>
     */
    private $eventContacts;

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setTable('events')
            ->setCustomRepositoryClass(EventRepository::class);

        $builder->addId();
        $builder->addField('eventExternalId', Types::STRING, ['columnName' => 'event_external_id', 'unique' => true]);
        $builder->addField('name', Types::STRING, ['columnName' => 'name']);
        $builder->addField('eventProgramUrlC', Types::STRING, ['columnName' => 'event_program_url_c', 'nullable' => true]);
        $builder->addField('historyUrlC', Types::STRING, ['columnName' => 'history_url_c', 'nullable' => true]);
        $builder->addField('eventSpeakersUrlC', Types::STRING, ['columnName' => 'event_speakers_url_c', 'nullable' => true]);
        $builder->addField('submissionUrlC', Types::STRING, ['columnName' => 'submission_url_c', 'nullable' => true]);
        $builder->addField('eventFaqUrlC', Types::STRING, ['columnName' => 'event_faq_url_c', 'nullable' => true]);
        $builder->addField('eventVenueUrlC', Types::STRING, ['columnName' => 'event_venue_url_c', 'nullable' => true]);
        $builder->addField('visaUrlC', Types::STRING, ['columnName' => 'visa_url_c', 'nullable' => true]);
        $builder->addField('registrationUrlC', Types::STRING, ['columnName' => 'registration_url_c', 'nullable' => true]);
        $builder->addField('eventFacebookUrlC', Types::STRING, ['columnName' => 'event_facebook_url_c', 'nullable' => true]);
        $builder->addField('eventFeedbackUrlC', Types::STRING, ['columnName' => 'event_feedback_url_c', 'nullable' => true]);
        $builder->addField('eventTestimonialsUrlC', Types::STRING, ['columnName' => 'event_testimonials_url_c', 'nullable' => true]);
        $builder->addField('eventWebsiteUrlC', Types::STRING, ['columnName' => 'event_website_url_c', 'nullable' => true]);
        $builder->addField('easyPaymentUrlC', Types::STRING, ['columnName' => 'easy_payment_url_c', 'nullable' => true]);
        $builder->addField('declineRedirect', Types::STRING, ['columnName' => 'decline_redirect', 'nullable' => true]);
        $builder->addField('acceptRedirect', Types::STRING, ['columnName' => 'accept_redirect', 'nullable' => true]);
        $builder->addField('eventManagerEmailC', Types::STRING, ['columnName' => 'event_manager_email_c', 'nullable' => true]);
        $builder->addField('isbnNumberC', Types::STRING, ['columnName' => 'isbn_number_c', 'nullable' => true]);
        $builder->addField('eventWireTransferDataC', Types::TEXT, ['columnName' => 'event_wire_transfer_data_c', 'nullable' => true]);
        $builder->addField('aboutEventC', Types::TEXT, ['columnName' => 'about_event_c', 'nullable' => true]);
        $builder->addField('description', Types::TEXT, ['columnName' => 'description', 'nullable' => true]);
        $builder->addField('durationMinutes', Types::INTEGER, ['columnName' => 'duration_minutes', 'nullable' => true]);
        $builder->addField('durationHours', Types::INTEGER, ['columnName' => 'duration_hours', 'nullable' => true]);
        $builder->addField('abstractBookImageC', Types::STRING, ['columnName' => 'abstract_book_image_c', 'nullable' => true]);
        $builder->addField('dateModified', Types::DATETIME_MUTABLE, ['columnName' => 'date_modified', 'nullable' => true]);
        $builder->addField('dateEntered', Types::DATETIME_MUTABLE, ['columnName' => 'date_entered', 'nullable' => true]);
        $builder->addField('earlyBirdRegDeadlineC', Types::DATE_MUTABLE, ['columnName' => 'early_bird_reg_deadline_c', 'nullable' => true]);
        $builder->addField('eventStartDateC', Types::DATE_MUTABLE, ['columnName' => 'event_start_date_c', 'nullable' => true]);
        $builder->addField('submissionDeadlineC', Types::DATE_MUTABLE, ['columnName' => 'submission_deadline_c', 'nullable' => true]);
        $builder->addField('eventEndDateC', Types::DATE_MUTABLE, ['columnName' => 'event_end_date_c', 'nullable' => true]);
        $builder->addField('earlyRegDeadlineC', Types::DATE_MUTABLE, ['columnName' => 'early_reg_deadline_c', 'nullable' => true]);
        $builder->addField('finalRegDeadlineC', Types::DATE_MUTABLE, ['columnName' => 'final_reg_deadline_c', 'nullable' => true]);
        $builder->addField('currencyId', Types::STRING, ['columnName' => 'currency_id', 'nullable' => true]);
        $builder->addField('budget', Types::DECIMAL, ['columnName' => 'budget', 'nullable' => true, 'precision' => 10, 'scale' => 2]);
        $builder->addField('deleted', Types::BOOLEAN, ['columnName' => 'deleted', 'nullable' => false, 'default' => false]);
        $builder->addField('eventOrganizerC', Types::STRING, ['columnName' => 'event_organizer_c', 'nullable' => true]);
        $builder->addField('eventRoundC', Types::STRING, ['columnName' => 'event_round_c', 'nullable' => true]);
        $builder->addField('eventManagerNameC', Types::STRING, ['columnName' => 'event_manager_name_c', 'nullable' => true]);
        $builder->addField('eventCityC', Types::STRING, ['columnName' => 'event_city_c', 'nullable' => true]);
        $builder->addField('eventFieldC', Types::STRING, ['columnName' => 'event_field_c', 'nullable' => true]);
        $builder->addField('eventCountryC', Types::STRING, ['columnName' => 'event_country_c', 'nullable' => true]);
        $builder->addField('activityStatusType', Types::STRING, ['columnName' => 'activity_status_type', 'nullable' => true]);
        $builder->addField('inviteTemplates', Types::STRING, ['columnName' => 'invite_templates', 'nullable' => true]);
        $builder->addField('createdAt', Types::DATETIME_MUTABLE, ['columnName' => 'created_at', 'nullable' => true]);
        $builder->addField('updatedAt', Types::DATETIME_MUTABLE, ['columnName' => 'updated_at', 'nullable' => true]);

        $builder->createOneToMany('eventContacts', EventContact::class)
            ->mappedBy('event')
            // Removing an event should delete its associations but not cascade other operations
            ->cascadeRemove()
            ->fetchExtraLazy()
            ->build();
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('eventExternalId', new NotBlank(['message' => 'mautic.events.event_external_id.required']));
        $metadata->addPropertyConstraint('name', new NotBlank(['message' => 'mautic.events.name.required']));
    }

    /**
     * Prepares the metadata for API usage.
     */
    public static function loadApiMetadata(ApiMetadataDriver $metadata): void
    {
        $metadata->setGroupPrefix('event')
            ->addListProperties([
                'id',
                'eventExternalId',
                'name',
                'eventProgramUrlC',
                'historyUrlC',
                'eventSpeakersUrlC',
                'submissionUrlC',
                'eventFaqUrlC',
                'eventVenueUrlC',
                'visaUrlC',
                'registrationUrlC',
                'eventFacebookUrlC',
                'eventFeedbackUrlC',
                'eventTestimonialsUrlC',
                'eventWebsiteUrlC',
                'easyPaymentUrlC',
                'declineRedirect',
                'acceptRedirect',
                'eventManagerEmailC',
                'isbnNumberC',
                'eventWireTransferDataC',
                'aboutEventC',
                'description',
                'durationMinutes',
                'durationHours',
                'abstractBookImageC',
                'dateModified',
                'dateEntered',
                'earlyBirdRegDeadlineC',
                'eventStartDateC',
                'submissionDeadlineC',
                'eventEndDateC',
                'earlyRegDeadlineC',
                'finalRegDeadlineC',
                'currencyId',
                'budget',
                'deleted',
                'eventOrganizerC',
                'eventRoundC',
                'eventManagerNameC',
                'eventCityC',
                'eventFieldC',
                'eventCountryC',
                'activityStatusType',
                'inviteTemplates',
            ])
            ->addProperties([
                'createdAt',
                'updatedAt',
            ])
            ->build();
    }

    public function __construct()
    {
        $this->eventContacts = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->dateEntered = new \DateTime();
        $this->dateModified = new \DateTime();
        $this->deleted = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    /**
     * @return Collection<int, EventContact>
     */
    public function getEventContacts(): Collection
    {
        return $this->eventContacts;
    }

    public function addEventContact(EventContact $eventContact): self
    {
        if (!$this->eventContacts->contains($eventContact)) {
            $this->eventContacts->add($eventContact);
            $eventContact->setEvent($this);
        }

        return $this;
    }

    public function removeEventContact(EventContact $eventContact): self
    {
        if ($this->eventContacts->removeElement($eventContact)) {
            $eventContact->setEvent($this);
        }

        return $this;
    }

    public function getEventExternalId(): ?string
    {
        return $this->eventExternalId;
    }

    public function setEventExternalId(string $eventExternalId): self
    {
        $this->eventExternalId = $eventExternalId;
        $this->updatedAt = new \DateTime();
        return $this;
    }


    public function getEventProgramUrlC(): ?string
    {
        return $this->eventProgramUrlC;
    }

    public function setEventProgramUrlC(?string $eventProgramUrlC): self
    {
        $this->eventProgramUrlC = $eventProgramUrlC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getHistoryUrlC(): ?string
    {
        return $this->historyUrlC;
    }

    public function setHistoryUrlC(?string $historyUrlC): self
    {
        $this->historyUrlC = $historyUrlC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventSpeakersUrlC(): ?string
    {
        return $this->eventSpeakersUrlC;
    }

    public function setEventSpeakersUrlC(?string $eventSpeakersUrlC): self
    {
        $this->eventSpeakersUrlC = $eventSpeakersUrlC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getSubmissionUrlC(): ?string
    {
        return $this->submissionUrlC;
    }

    public function setSubmissionUrlC(?string $submissionUrlC): self
    {
        $this->submissionUrlC = $submissionUrlC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventFaqUrlC(): ?string
    {
        return $this->eventFaqUrlC;
    }

    public function setEventFaqUrlC(?string $eventFaqUrlC): self
    {
        $this->eventFaqUrlC = $eventFaqUrlC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventVenueUrlC(): ?string
    {
        return $this->eventVenueUrlC;
    }

    public function setEventVenueUrlC(?string $eventVenueUrlC): self
    {
        $this->eventVenueUrlC = $eventVenueUrlC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getVisaUrlC(): ?string
    {
        return $this->visaUrlC;
    }

    public function setVisaUrlC(?string $visaUrlC): self
    {
        $this->visaUrlC = $visaUrlC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getRegistrationUrlC(): ?string
    {
        return $this->registrationUrlC;
    }

    public function setRegistrationUrlC(?string $registrationUrlC): self
    {
        $this->registrationUrlC = $registrationUrlC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventFacebookUrlC(): ?string
    {
        return $this->eventFacebookUrlC;
    }

    public function setEventFacebookUrlC(?string $eventFacebookUrlC): self
    {
        $this->eventFacebookUrlC = $eventFacebookUrlC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventFeedbackUrlC(): ?string
    {
        return $this->eventFeedbackUrlC;
    }

    public function setEventFeedbackUrlC(?string $eventFeedbackUrlC): self
    {
        $this->eventFeedbackUrlC = $eventFeedbackUrlC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventTestimonialsUrlC(): ?string
    {
        return $this->eventTestimonialsUrlC;
    }

    public function setEventTestimonialsUrlC(?string $eventTestimonialsUrlC): self
    {
        $this->eventTestimonialsUrlC = $eventTestimonialsUrlC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getWebsiteUrlC(): ?string
    {
        return $this->eventWebsiteUrlC;
    }

    public function setWebsiteUrlC(?string $eventWebsiteUrlC): self
    {
        $this->eventWebsiteUrlC = $eventWebsiteUrlC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEasyPaymentUrlC(): ?string
    {
        return $this->easyPaymentUrlC;
    }

    public function setEasyPaymentUrlC(?string $easyPaymentUrlC): self
    {
        $this->easyPaymentUrlC = $easyPaymentUrlC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getDeclineRedirect(): ?string
    {
        return $this->declineRedirect;
    }

    public function setDeclineRedirect(?string $declineRedirect): self
    {
        $this->declineRedirect = $declineRedirect;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getAcceptRedirect(): ?string
    {
        return $this->acceptRedirect;
    }

    public function setAcceptRedirect(?string $acceptRedirect): self
    {
        $this->acceptRedirect = $acceptRedirect;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventManagerEmailC(): ?string
    {
        return $this->eventManagerEmailC;
    }

    public function setEventManagerEmailC(?string $eventManagerEmailC): self
    {
        $this->eventManagerEmailC = $eventManagerEmailC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getIsbnNumberC(): ?string
    {
        return $this->isbnNumberC;
    }

    public function setIsbnNumberC(?string $isbnNumberC): self
    {
        $this->isbnNumberC = $isbnNumberC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventWireTransferDataC(): ?string
    {
        return $this->eventWireTransferDataC;
    }

    public function setEventWireTransferDataC(?string $eventWireTransferDataC): self
    {
        $this->eventWireTransferDataC = $eventWireTransferDataC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getAboutEventC(): ?string
    {
        return $this->aboutEventC;
    }

    public function setAboutEventC(?string $aboutEventC): self
    {
        $this->aboutEventC = $aboutEventC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(?int $durationMinutes): self
    {
        $this->durationMinutes = $durationMinutes;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getDurationHours(): ?int
    {
        return $this->durationHours;
    }

    public function setDurationHours(?int $durationHours): self
    {
        $this->durationHours = $durationHours;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getAbstractBookImageC(): ?string
    {
        return $this->abstractBookImageC;
    }

    public function setAbstractBookImageC(?string $abstractBookImageC): self
    {
        $this->abstractBookImageC = $abstractBookImageC;
        $this->updatedAt = new \DateTime();
        return $this;
    }


    public function getDateModified(): ?\DateTime
    {
        return $this->dateModified;
    }

    public function setDateModified(?\DateTime $dateModified): self
    {
        $this->dateModified = $dateModified;
        return $this;
    }

    public function getDateEntered(): ?\DateTime
    {
        return $this->dateEntered;
    }

    public function setDateEntered(?\DateTime $dateEntered): self
    {
        $this->dateEntered = $dateEntered;
        return $this;
    }

    public function getEarlyBirdRegDeadlineC(): ?\DateTime
    {
        return $this->earlyBirdRegDeadlineC;
    }

    public function setEarlyBirdRegDeadlineC(?\DateTime $earlyBirdRegDeadlineC): self
    {
        $this->earlyBirdRegDeadlineC = $earlyBirdRegDeadlineC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventStartDateC(): ?\DateTime
    {
        return $this->eventStartDateC;
    }

    public function setEventStartDateC(?\DateTime $eventStartDateC): self
    {
        $this->eventStartDateC = $eventStartDateC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getSubmissionDeadlineC(): ?\DateTime
    {
        return $this->submissionDeadlineC;
    }

    public function setSubmissionDeadlineC(?\DateTime $submissionDeadlineC): self
    {
        $this->submissionDeadlineC = $submissionDeadlineC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventEndDateC(): ?\DateTime
    {
        return $this->eventEndDateC;
    }

    public function setEventEndDateC(?\DateTime $eventEndDateC): self
    {
        $this->eventEndDateC = $eventEndDateC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEarlyRegDeadlineC(): ?\DateTime
    {
        return $this->earlyRegDeadlineC;
    }

    public function setEarlyRegDeadlineC(?\DateTime $earlyRegDeadlineC): self
    {
        $this->earlyRegDeadlineC = $earlyRegDeadlineC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getFinalRegDeadlineC(): ?\DateTime
    {
        return $this->finalRegDeadlineC;
    }

    public function setFinalRegDeadlineC(?\DateTime $finalRegDeadlineC): self
    {
        $this->finalRegDeadlineC = $finalRegDeadlineC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getCurrencyId(): ?string
    {
        return $this->currencyId;
    }

    public function setCurrencyId(?string $currencyId): self
    {
        $this->currencyId = $currencyId;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): self
    {
        $this->budget = $budget;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventOrganizerC(): ?string
    {
        return $this->eventOrganizerC;
    }

    public function setEventOrganizerC(?string $eventOrganizerC): self
    {
        $this->eventOrganizerC = $eventOrganizerC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventRoundC(): ?string
    {
        return $this->eventRoundC;
    }

    public function setEventRoundC(?string $eventRoundC): self
    {
        $this->eventRoundC = $eventRoundC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventManagerNameC(): ?string
    {
        return $this->eventManagerNameC;
    }

    public function setEventManagerNameC(?string $eventManagerNameC): self
    {
        $this->eventManagerNameC = $eventManagerNameC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventCityC(): ?string
    {
        return $this->eventCityC;
    }

    public function setEventCityC(?string $eventCityC): self
    {
        $this->eventCityC = $eventCityC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventFieldC(): ?string
    {
        return $this->eventFieldC;
    }

    public function setEventFieldC(?string $eventFieldC): self
    {
        $this->eventFieldC = $eventFieldC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getEventCountryC(): ?string
    {
        return $this->eventCountryC;
    }

    public function setEventCountryC(?string $eventCountryC): self
    {
        $this->eventCountryC = $eventCountryC;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getActivityStatusType(): ?string
    {
        return $this->activityStatusType;
    }

    public function setActivityStatusType(?string $activityStatusType): self
    {
        $this->activityStatusType = $activityStatusType;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getInviteTemplates(): ?string
    {
        return $this->inviteTemplates;
    }

    public function setInviteTemplates(?string $inviteTemplates): self
    {
        $this->inviteTemplates = $inviteTemplates;
        $this->updatedAt = new \DateTime();
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function __call($name, $arguments)
    {
        $defaults = [
            'getCreatedBy'      => null,
            'getDateAdded'      => null,
            'getDateModified'   => null,
            'getCreatedByUser'  => null,
            'getModifiedBy'     => null,
            'getModifiedByUser' => null,
            'getCheckedOut'     => null,
            'getCheckedOutBy'   => null,
            'getCheckedOutByUser' => null,
            'isPublished'       => true,
        ];

        if (array_key_exists($name, $defaults)) {
            return $defaults[$name];
        }

        return parent::__call($name, $arguments);
    }
}
