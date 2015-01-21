<?php
namespace Concrete\Core\Updater\Migrations\Migrations;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version574a1 extends AbstractMigration
{

    public function getName()
    {
        return '20150113000000';
    }

    public function up(Schema $schema)
    {
        \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema(array(
           'Calendars', 'CalendarEvents', 'CalendarEventRepetitions', 'CalendarEventOccurrences',
           'CalendarEventAttributeValues'
        ));
    }

    public function down(Schema $schema)
    {
    }
}
