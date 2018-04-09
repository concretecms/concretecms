<?php

namespace Concrete\Core\Updater\Migrations\Migrations;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Backup\ContentImporter;
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
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Package;
use Concrete\Core\Updater\Migrations\AbstractMigration;
use Concrete\Core\Updater\Migrations\LongRunningMigrationInterface;
use Concrete\Core\Updater\Migrations\RepeatableMigrationInterface;

class Version20171110032423 extends AbstractMigration implements RepeatableMigrationInterface, LongRunningMigrationInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Updater\Migrations\AbstractMigration::upgradeDatabase()
     */
    public function upgradeDatabase()
    {
        if ($this->connection->getSchemaManager()->tablesExist('CalendarEventVersions')) {
            $events = (int) $this->connection->fetchColumn('select count(*) from CalendarEventVersions');
        } else {
            $events = null;
        }
        if ($events === null || $events === 0) {
            $this->migrateCalendar();
        }
    }

    protected function migrateCalendar()
    {
        $this->addEarlyCalendarFunctionality();
        // first, let's see whether the concrete5 calendar is installed.
        $pkg = Package::getByHandle('calendar');
        if ($pkg) {
            $this->connection->Execute('set foreign_key_checks = 0');

            // let's uninstall the package.
            $this->uninstallLegacyCalendar($pkg);

            // Now, let's stash all the data from the legacy calendar add-on in somebackup tables.
            $this->backupLegacyCalendar();

            // now add the calendar functionality
            $this->addCalendarFunctionality();

            // now, take existing calendar attribute keys and turn them into 8.3 keys
            $this->updateAttributeKeys($pkg);

            // leaving data in backup tables so that we can use the dashboard import to migrate it.

            $this->connection->Execute('set foreign_key_checks = 1');
        } else {
            $this->addCalendarFunctionality();
        }
    }

    protected function addEarlyCalendarFunctionality()
    {
        $this->output(t('Installing calendar attribute category table...'));
        $this->refreshEntities([
            EventKey::class,
        ]);
    }

    protected function addCalendarFunctionality()
    {
        $this->output(t('Installing calendar entities...'));
        $this->refreshEntities([
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
        $this->output(t('Installing calendar XML...'));
        $importer = new ContentImporter();
        $importer->importContentFile(DIR_BASE_CORE . '/config/install/upgrade/calendar.xml');
    }

    protected function backupLegacyCalendar()
    {
        $this->output(t('Backing up legacy calendar...'));
        $sm = $this->connection->getSchemaManager();
        foreach ([
            'CalendarEventAttributeValues',
            'CalendarEventOccurrences',
            'CalendarEventRepetitions',
            'CalendarEventSearchIndexAttributes',
            'CalendarEvents',
            'Calendars',
        ] as $tableName) {
            $newTableName = '_' . $tableName;
            if ($sm->tablesExist($newTableName)) {
                continue;
            }
            foreach ($sm->listTableForeignKeys($tableName) as $foreignKey) {
                $sm->dropForeignKey($foreignKey, $tableName);
            }
            $sm->renameTable($tableName, $newTableName);
        }
    }

    protected function uninstallLegacyCalendar(\Concrete\Core\Entity\Package $pkg)
    {
        $this->output('Uninstalling legacy calendar package...');
        $this->output('Removing pages...');
        $r = $this->connection->executeQuery('select cID from Pages where pkgID = ?', [$pkg->getPackageID()]);
        while ($row = $r->fetch()) {
            $page = Page::getByID($row['cID']);
            if ($page && !$page->isError()) {
                $page->delete();
            }
        }
        $this->output('Updating attribute categories...');
        $this->connection->executeQuery('delete from AttributeKeyCategories where pkgID = ?', [$pkg->getPackageID()]);
        $this->output('Updating block types...');
        $this->connection->executeQuery('delete from BlockTypes where pkgID = ?', [$pkg->getPackageID()]);
        $this->output(t('Uninstalling calendar package (ID %s)', $pkg->getPackageID()));
        $this->connection->executeQuery('delete from Packages where pkgID = ?', [$pkg->getPackageID()]);
    }

    protected function updateAttributeKeys($pkg)
    {
        $this->output(t('Updating attribute keys from legacy to 8.3.'));
        $category = Category::getByHandle('event');
        $r = $this->connection->executeQuery('select akID from AttributeKeys ak left join AttributeKeyCategories akc on ak.akCategoryID = akc.akCategoryID where akCategory = "legacykey" and akc.akCategoryID is null');
        while ($row = $r->fetch()) {
            $cnt = $this->connection->fetchColumn('select count(akID) from CalendarEventAttributeKeys where akID = ?', [$row['akID']]);
            if (!$cnt) {
                $this->connection->executeQuery('delete from LegacyAttributeKeys where akID = ?', [$row['akID']]);
                $this->connection->executeQuery('update AttributeKeys set pkgID = null, akCategoryID = ?, akCategory = ? where akID = ?',
                    [$category->getAttributeKeyCategoryID(), 'eventkey', $row['akID']]
                );
                $this->connection->insert('CalendarEventAttributeKeys', ['akID' => $row['akID']]);
            }
        }

        $r = $this->connection->executeQuery('select asID from AttributeSets ats left join AttributeKeyCategories akc on ats.akCategoryID = akc.akCategoryID where akc.akCategoryID is null');
        while ($row = $r->fetch()) {
            $this->connection->executeQuery('update AttributeSets set pkgID = null, akCategoryID = ? where asID = ?',
                [$category->getAttributeKeyCategoryID(), $row['asID']]
            );
        }
    }
}
