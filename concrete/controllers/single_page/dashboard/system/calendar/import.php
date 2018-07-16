<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Calendar;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Page\Controller\DashboardPageController;
use Doctrine\DBAL\Connection;

class Import extends DashboardPageController
{

    public function submit()
    {
        $site = $this->app->make('site')->getSite();
        if ($this->token->validate('submit')) {
            /**
             * @var $db Connection
             */
            $db = $this->app->make(Connection::class);
            $db->executeQuery('set foreign_key_checks = 0');
            if ($db->tableExists('_CalendarEventOccurrences')) {
                $db->executeQuery('delete o from _CalendarEventOccurrences o left join _CalendarEvents e on o.eventID = e.eventID where e.eventID is null');
            }

            $r1 = $db->executeQuery('select * from _Calendars order by caID asc');
            while ($row1 = $r1->fetch()) {
                $r2 = $db->executeQuery('select * from _CalendarEvents where caID = ? order by eventID asc', [$row1['caID']]);
                while ($row2 = $r2->fetch()) {

                    $db->insert('CalendarEvents', [
                        'eventID' => $row2['eventID'],
                        'caID' => $row1['caID']
                    ]);

                    $db->insert('CalendarEventVersions', [
                        'evDateAdded' => date('Y-m-d H:i:s'),
                        'evActivateDateTime' => date("Y-m-d H:i:s"),
                        'evIsApproved' => 1,
                        'evDescription' => $row2['description'],
                        'evName' => $row2['name'],
                        'evRelatedPageRelationType' => $row['evRelatedPageRelationType'],
                        'cID' => $row2['cID'],
                        'eventID' => $row2['eventID'],
                        'uID' => 1
                    ]);
                    $eventVersionID = $db->lastInsertId();

                    $r3 = $db->executeQuery('select * from _CalendarEventRepetitions where repetitionID = ?', [$row2['repetitionID']]);
                    while ($row3 = $r3->fetch()) {
                        $db->insert('CalendarEventRepetitions', [
                            'repetitionID' => $row3['repetitionID'],
                            'repetitionObject' => $row3['repetitionObject'],
                        ]);
                        $db->insert('CalendarEventVersionRepetitions', [
                            'versionRepetitionID' => $row3['repetitionID'],
                            'repetitionID' => $row3['repetitionID'],
                            'eventVersionID' => $eventVersionID
                        ]);
                    }

                    $r3 = $db->executeQuery('select * from _CalendarEventOccurrences where eventID = ?', [$row2['eventID']]);
                    while ($row3 = $r3->fetch()) {
                        $db->insert('CalendarEventOccurrences', [
                            'occurrenceID' => $row3['occurrenceID'],
                            'startTime' => $row3['startTime'],
                            'endTime' => $row3['endTime'],
                            'cancelled' => $row3['cancelled'],
                            'repetitionID' => $row2['repetitionID'],
                        ]);
                        $db->insert('CalendarEventVersionOccurrences', [
                            'versionOccurrenceID' => $row3['occurrenceID'],
                            'occurrenceID' => $row3['occurrenceID'],
                            'eventVersionID' => $eventVersionID
                        ]);
                    }

                    $r3 = $db->executeQuery('select * from _CalendarEventAttributeValues where eventID = ?', [$row2['eventID']]);
                    while ($row3 = $r3->fetch()) {
                        $db->insert('CalendarEventVersionAttributeValues', [
                            'eventVersionID' => $eventVersionID,
                            'akID' => $row3['akID'],
                            'avID' => $row3['avID'],
                        ]);
                    }
                }
                $db->executeQuery('insert into Calendars (caID, caName, siteID) values (?, ?, ?)', [
                    $row1['caID'],
                    $row1['caName'],
                    $site->getSiteID()
                ]);
            }

            $db->executeQuery("update CalendarEventRepetitions set repetitionObject = replace(repetitionObject, 'O:43:\"PortlandLabs\\\Calendar\\\Event\\\EventRepetition', 'O:44:\"Concrete\\\Core\\\Calendar\\\Event\\\EventRepetition')");

            $db->executeQuery('drop table if exists _CalendarEventAttributeValues');
            $db->executeQuery('drop table if exists _CalendarEventOccurrences');
            $db->executeQuery('drop table if exists _CalendarEventRepetitions');
            $db->executeQuery('drop table if exists _CalendarEventSearchIndexAttributes');
            $db->executeQuery('drop table if exists _CalendarEvents');
            $db->executeQuery('drop table if exists _Calendars');

            $this->flash('success', t('Data imported successfully.'));
            return $this->redirect('/dashboard/system/calendar/import', 'view');
        }
    }

    public function view()
    {
        /**
         * @var $db Connection
         */
        $db = $this->app->make(Connection::class);
        if ($db->tableExists('_CalendarEvents')) {
            $r = $db->fetchColumn('select count(*) from _Calendars');
            $this->set('numCalendars', $r);
        }
    }
}
