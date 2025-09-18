<?php

namespace MauticPlugin\MauticEventsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<mixed>
 */
class EventFieldsType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => $this->getEventFieldChoices(),
            'expanded' => false,
            'multiple' => false,
            'label_attr' => ['class' => 'control-label'],
            'attr' => ['class' => 'form-control'],
        ]);
    }

    /**
     * Get all available event fields for selection.
     */
    private function getEventFieldChoices(): array
    {
        return [
            // Basic Information
            'mautic.events.segment.event_name' => 'name',
            'mautic.events.segment.event_description' => 'description',
            'mautic.events.segment.event_about_event_c' => 'aboutEventC',
            'mautic.events.segment.event_external_id' => 'eventExternalId',

            // URLs
            'mautic.events.segment.event_program_url_c' => 'eventProgramUrlC',
            'mautic.events.segment.event_history_url_c' => 'historyUrlC',
            'mautic.events.segment.event_speakers_url_c' => 'eventSpeakersUrlC',
            'mautic.events.segment.event_submission_url_c' => 'submissionUrlC',
            'mautic.events.segment.event_faq_url_c' => 'eventFaqUrlC',
            'mautic.events.segment.event_venue_url_c' => 'eventVenueUrlC',
            'mautic.events.segment.event_visa_url_c' => 'visaUrlC',
            'mautic.events.segment.event_registration_url_c' => 'registrationUrlC',
            'mautic.events.segment.event_facebook_url_c' => 'eventFacebookUrlC',
            'mautic.events.segment.event_feedback_url_c' => 'eventFeedbackUrlC',
            'mautic.events.segment.event_testimonials_url_c' => 'eventTestimonialsUrlC',
            'mautic.events.segment.event_website_url_c' => 'websiteUrlC',
            'mautic.events.segment.event_easy_payment_url_c' => 'easyPaymentUrlC',

            // Redirect URLs
            'mautic.events.segment.event_decline_redirect' => 'declineRedirect',
            'mautic.events.segment.event_accept_redirect' => 'acceptRedirect',

            // Contact Information
            'mautic.events.segment.event_manager_email_c' => 'eventManagerEmailC',
            'mautic.events.segment.event_manager_name_c' => 'eventManagerNameC',
            'mautic.events.segment.event_organizer_c' => 'eventOrganizerC',

            // Details
            'mautic.events.segment.event_isbn_number_c' => 'isbnNumberC',
            'mautic.events.segment.event_wire_transfer_data_c' => 'eventWireTransferDataC',
            'mautic.events.segment.event_abstract_book_image_c' => 'abstractBookImageC',

            // Duration
            'mautic.events.segment.event_duration_minutes' => 'durationMinutes',
            'mautic.events.segment.event_duration_hours' => 'durationHours',

            // Dates
            'mautic.events.segment.event_date_modified' => 'dateModified',
            'mautic.events.segment.event_date_entered' => 'dateEntered',
            'mautic.events.segment.event_early_bird_reg_deadline_c' => 'earlyBirdRegDeadlineC',
            'mautic.events.segment.event_start_date_c' => 'eventStartDateC',
            'mautic.events.segment.event_submission_deadline_c' => 'submissionDeadlineC',
            'mautic.events.segment.event_end_date_c' => 'eventEndDateC',
            'mautic.events.segment.event_early_reg_deadline_c' => 'earlyRegDeadlineC',
            'mautic.events.segment.event_final_reg_deadline_c' => 'finalRegDeadlineC',
            'mautic.events.segment.event_created_at' => 'createdAt',
            'mautic.events.segment.event_updated_at' => 'updatedAt',

            // Location
            'mautic.events.segment.event_city_c' => 'eventCityC',
            'mautic.events.segment.event_country_c' => 'eventCountryC',
            'mautic.events.segment.event_field_c' => 'eventFieldC',

            // Financial
            'mautic.events.segment.event_currency_id' => 'currencyId',
            'mautic.events.segment.event_budget' => 'budget',

            // Status
            'mautic.events.segment.event_deleted' => 'deleted',
            'mautic.events.segment.event_round_c' => 'eventRoundC',
            'mautic.events.segment.event_activity_status_type' => 'activityStatusType',
            'mautic.events.segment.event_invite_templates' => 'inviteTemplates',
        ];
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'event_fields';
    }
}