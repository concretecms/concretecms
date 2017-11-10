<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Backup\ContentImporter;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Concrete\Core\Entity\Attribute\Key\EventKey;
use Concrete\Core\Entity\Attribute\Value\EventValue;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventOccurrence;
use Concrete\Core\Entity\Calendar\CalendarEventRepetition;
use Concrete\Core\Entity\Calendar\CalendarEventVersion;
use Concrete\Core\Entity\Calendar\CalendarEventVersionOccurrence;
use Concrete\Core\Entity\Calendar\CalendarEventVersionRepetition;
use Concrete\Core\Entity\Calendar\CalendarEventWorkflowProgress;
use Concrete\Core\Entity\Calendar\CalendarPermissionAssignment;
use Concrete\Core\Entity\Calendar\CalendarRelatedEvent;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171110032423 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->refreshEntities([
            EventKey::class,
            EventValue::class,
            Calendar::class,
            CalendarEvent::class,
            CalendarEventOccurrence::class,
            CalendarEventRepetition::class,
            CalendarEventVersion::class,
            CalendarEventVersionOccurrence::class,
            CalendarEventVersionRepetition::class,
            CalendarEventWorkflowProgress::class,
            CalendarRelatedEvent::class,
            CalendarPermissionAssignment::class,
        ]);
        $importer = new ContentImporter();
        $importer->importContentFile(DIR_BASE_CORE . '/config/install/upgrade/calendar.xml');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
