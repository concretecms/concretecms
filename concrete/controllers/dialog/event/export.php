<?php

namespace Concrete\Controller\Dialog\Event;

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Calendar\Event\EventService;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventVersion;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Support\Facade\Url;
use Sabre\VObject\Component\VCalendar;
use DateTime;
use DateTimeZone;

class Export extends BackendInterfaceController
{
    /** @var EventService */
    protected $eventService;
    /** @var ResponseFactory */
    protected $responseFactory;

    public function __construct()
    {
        parent::__construct();
        $this->eventService = $this->app->make(EventService::class);
        $this->responseFactory = $this->app->make(ResponseFactory::class);
    }

    public function export()
    {
        $eventId = $this->request->query->get("eventID");

        $event = $this->eventService->getByID($eventId, EventService::EVENT_VERSION_RECENT);

        if ($event instanceof CalendarEvent) {
            $approvedEventVersion = $event->getApprovedVersion();

            if ($approvedEventVersion instanceof CalendarEventVersion) {
                // create the iCalendar-Object
                $vCalendar = new VCalendar();

                $i = 0;

                foreach ($approvedEventVersion->getRepetitions() as $repetition) {
                    // attributes and categories are ignored because they are not supported in the iCalendar format
                    // @see https://tools.ietf.org/html/rfc2446

                    /** @noinspection PhpUnhandledExceptionInspection */
                    /** @noinspection HtmlRequiredLangAttribute */
                    $arrEvent = [
                        'SUMMARY' => $approvedEventVersion->getName(),
                        'DESCRIPTION' => strip_tags($approvedEventVersion->getDescription()),
                        // Add HTML description if supported (https://stackoverflow.com/questions/854036/html-in-ical-attachment)
                        'X-ALT-DESC' => 'FMTTYPE=text/html:<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN"><HTML>' . $approvedEventVersion->getDescription() . '</HTML>',
                        'CREATED' => $approvedEventVersion->getDateAdded(),
                        'URL' => Url::to($approvedEventVersion->getPageObject()),
                        'DTSTART' => new DateTime($repetition->getStartDate(), new DateTimeZone($event->getCalendar()->getTimezone())),
                        'DTEND' => new DateTime($repetition->getEndDate(), new DateTimeZone($event->getCalendar()->getTimezone())),
                        'SEQUENCE' => $i++
                    ];

                    $author = $approvedEventVersion->getAuthor();

                    if ($author instanceof User) {
                        $arrEvent['ORGANIZER'] = "CN=" . $approvedEventVersion->getAuthor()->getUserName() . ":MAILTO:" . $approvedEventVersion->getAuthor()->getUserEmail();
                    }

                    $vCalendar->add('VEVENT', $arrEvent);
                }

                return $this->responseFactory->create($vCalendar->serialize(), Response::HTTP_OK, [
                    "Content-Type" => "text/calendar; charset=utf-8",
                    "Content-Disposition" => "inline; filename=\"" . $approvedEventVersion->getName() . ".ics\""
                ]);
            }
        }

        return $this->responseFactory->forbidden("");
    }

    protected function canAccess()
    {
        $eventId = $this->request->query->get("eventID");

        $event = $this->eventService->getByID($eventId, EventService::EVENT_VERSION_RECENT);

        if ($event instanceof CalendarEvent) {
            $calendar = $event->getCalendar();

            if ($calendar instanceof Calendar) {
                $permissionChecker = new Checker($calendar);

                $responseObject = $permissionChecker->getResponseObject();

                /** @noinspection PhpUnhandledExceptionInspection */
                return $responseObject->validate("view_calendar");
            }
        }

        return false;
    }


}
