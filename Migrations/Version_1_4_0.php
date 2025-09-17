<?php

declare(strict_types=1);

namespace MauticPlugin\MauticEventsBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration to remove date_start and date_end columns from events table
 */
final class Version_1_4_0 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove date_start and date_end columns from events table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('events');

        // Remove date_start and date_end columns
        if ($table->hasColumn('date_start')) {
            $table->dropColumn('date_start');
        }
        if ($table->hasColumn('date_end')) {
            $table->dropColumn('date_end');
        }
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('events');

        // Re-add date_start and date_end columns if needed for rollback
        $table->addColumn('date_start', 'datetime', ['nullable' => true]);
        $table->addColumn('date_end', 'datetime', ['nullable' => true]);
    }
}