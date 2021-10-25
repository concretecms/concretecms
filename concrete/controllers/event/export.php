<?php

namespace Concrete\Controller\Event;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Calendar\Event\EventService;
use Concrete\Core\Entity\Calendar\Calendar;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Calendar\CalendarEventVersion;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Support\Facade\Url;
use Sabre\VObject\Component\VCalendar;
use DateTime;
use DateTimeZone;

class Export implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    public function export()
    {
        $request = Request::createFromGlobals();

        $eventService = $this->app->make(EventService::class);
        $responseFactory = $this->app->make(ResponseFactory::class);

        $eventId = $request->query->get("eventID");

        $event = $eventService->getByID($eventId);

        if ($event instanceof CalendarEvent) {

            $calendar = $event->getCalendar();
            if ($calendar instanceof Calendar) {

                $permissions = new Checker($calendar);

                if ($permissions->canViewCalendar()) {
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
                                'X-ALT-DESC' => 'FMTTYPE=text/html:<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN"><HTML>' . $approvedEventVersion->getDescription(
                                    ) . '</HTML>',
                                'CREATED' => $approvedEventVersion->getDateAdded(),
                                'URL' => Url::to($approvedEventVersion->getPageObject()),
                                'DTSTART' => new DateTime(
                                    $repetition->getStartDate(),
                                    new DateTimeZone($event->getCalendar()->getTimezone())
                                ),
                                'DTEND' => new DateTime(
                                    $repetition->getEndDate(),
                                    new DateTimeZone($event->getCalendar()->getTimezone())
                                ),
                                'SEQUENCE' => $i++
                            ];

                            $vCalendar->add('VEVENT', $arrEvent);
                        }

                        return $responseFactory->create(
                            $vCalendar->serialize(),
                            Response::HTTP_OK,
                            [
                                "Content-Type" => "text/calendar; charset=utf-8",
                                "Content-Disposition" => "inline; filename=\"" . $approvedEventVersion->getName(
                                    ) . ".ics\""
                            ]
                        );
                    }
                }
            }
        }

        throw new UserMessageException(t('Access Denied.'));
    }


}
