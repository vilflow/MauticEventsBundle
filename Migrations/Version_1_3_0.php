<?php

declare(strict_types=1);

namespace MauticPlugin\MauticEventsBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to update Event entity fields - remove old fields and add new ones
 */
final class Version_1_3_0 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove old event fields and add new URL and other field types';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('events');

        // Remove old fields
        if ($table->hasColumn('website')) {
            $table->dropColumn('website');
        }
        if ($table->hasColumn('currency')) {
            $table->dropColumn('currency');
        }
        if ($table->hasColumn('country')) {
            $table->dropColumn('country');
        }
        if ($table->hasColumn('city')) {
            $table->dropColumn('city');
        }
        if ($table->hasColumn('registration_url')) {
            $table->dropColumn('registration_url');
        }
        if ($table->hasColumn('suitecrm_id')) {
            $table->dropColumn('suitecrm_id');
        }

        // Add new URL fields
        $table->addColumn('event_program_url_c', 'string', ['nullable' => true]);
        $table->addColumn('history_url_c', 'string', ['nullable' => true]);
        $table->addColumn('event_speakers_url_c', 'string', ['nullable' => true]);
        $table->addColumn('submission_url_c', 'string', ['nullable' => true]);
        $table->addColumn('event_faq_url_c', 'string', ['nullable' => true]);
        $table->addColumn('event_venue_url_c', 'string', ['nullable' => true]);
        $table->addColumn('visa_url_c', 'string', ['nullable' => true]);
        $table->addColumn('registration_url_c', 'string', ['nullable' => true]);
        $table->addColumn('event_facebook_url_c', 'string', ['nullable' => true]);
        $table->addColumn('event_feedback_url_c', 'string', ['nullable' => true]);
        $table->addColumn('event_testimonials_url_c', 'string', ['nullable' => true]);
        $table->addColumn('website_url_c', 'string', ['nullable' => true]);
        $table->addColumn('easy_payment_url_c', 'string', ['nullable' => true]);
        $table->addColumn('decline_redirect', 'string', ['nullable' => true]);
        $table->addColumn('accept_redirect', 'string', ['nullable' => true]);

        // Add new text fields
        $table->addColumn('event_manager_email_c', 'string', ['nullable' => true]);
        $table->addColumn('isbn_number_c', 'string', ['nullable' => true]);

        // Add new text area fields
        $table->addColumn('event_wire_transfer_data_c', 'text', ['nullable' => true]);
        $table->addColumn('about_event_c', 'text', ['nullable' => true]);
        $table->addColumn('description', 'text', ['nullable' => true]);

        // Add new integer fields
        $table->addColumn('duration_minutes', 'integer', ['nullable' => true]);
        $table->addColumn('duration_hours', 'integer', ['nullable' => true]);

        // Add new image field
        $table->addColumn('abstract_book_image_c', 'string', ['nullable' => true]);

        // Add new datetime fields
        $table->addColumn('date_end', 'datetime', ['nullable' => true]);
        $table->addColumn('date_start', 'datetime', ['nullable' => true]);
        $table->addColumn('date_modified', 'datetime', ['nullable' => true]);
        $table->addColumn('date_entered', 'datetime', ['nullable' => true]);

        // Add new date fields
        $table->addColumn('early_bird_reg_deadline_c', 'date', ['nullable' => true]);
        $table->addColumn('event_start_date_c', 'date', ['nullable' => true]);
        $table->addColumn('submission_deadline_c', 'date', ['nullable' => true]);
        $table->addColumn('event_end_date_c', 'date', ['nullable' => true]);
        $table->addColumn('early_reg_deadline_c', 'date', ['nullable' => true]);
        $table->addColumn('final_reg_deadline_c', 'date', ['nullable' => true]);

        // Add new currency and budget fields
        $table->addColumn('currency_id', 'string', ['nullable' => true]);
        $table->addColumn('budget', 'decimal', ['nullable' => true, 'precision' => 10, 'scale' => 2]);

        // Add new boolean field
        $table->addColumn('deleted', 'boolean', ['nullable' => false, 'default' => false]);
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('events');

        // Remove new fields
        $newFields = [
            'event_program_url_c', 'history_url_c', 'event_speakers_url_c', 'submission_url_c',
            'event_faq_url_c', 'event_venue_url_c', 'visa_url_c', 'registration_url_c',
            'event_facebook_url_c', 'event_feedback_url_c', 'event_testimonials_url_c',
            'website_url_c', 'easy_payment_url_c', 'decline_redirect', 'accept_redirect',
            'event_manager_email_c', 'isbn_number_c', 'event_wire_transfer_data_c',
            'about_event_c', 'description', 'duration_minutes', 'duration_hours',
            'abstract_book_image_c', 'date_end', 'date_start', 'date_modified', 'date_entered',
            'early_bird_reg_deadline_c', 'event_start_date_c', 'submission_deadline_c',
            'event_end_date_c', 'early_reg_deadline_c', 'final_reg_deadline_c',
            'currency_id', 'budget', 'deleted'
        ];

        foreach ($newFields as $field) {
            if ($table->hasColumn($field)) {
                $table->dropColumn($field);
            }
        }

        // Add back old fields
        $table->addColumn('website', 'string', ['nullable' => true]);
        $table->addColumn('currency', 'string', ['nullable' => true, 'length' => 3]);
        $table->addColumn('country', 'string', ['nullable' => true]);
        $table->addColumn('city', 'string', ['nullable' => true]);
        $table->addColumn('registration_url', 'string', ['nullable' => true]);
        $table->addColumn('suitecrm_id', 'string', ['nullable' => true]);
    }
}