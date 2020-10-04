<?php

namespace Concrete\Core\Calendar\Event\Summary\Template\Command;

use Concrete\Core\Calendar\Event\EventService;
use Concrete\Core\Entity\Calendar\CalendarEvent;
use Concrete\Core\Entity\Page\Summary\CustomPageTemplateCollection;
use Concrete\Core\Entity\Summary\Template;
use Doctrine\ORM\EntityManager;

class DisableCustomCalendarEventSummaryTemplatesCommandHandler
{

    /**
     * @var EventService
     */
    protected $eventService;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager, EventService $eventService)
    {
        $this->entityManager = $entityManager;
        $this->eventService = $eventService;
    }

    protected function clearCustomCollection($eventID)
    {
        $event = $this->eventService->getByID($eventID);
        if ($event) {
            /**
             * @var $event CalendarEvent
             */
            $event->setHasCustomSummaryTemplates(false);
            $event->clearCustomSummaryTemplates();
            $this->entityManager->persist($event);
            $this->entityManager->flush();
        }
    }

    public function __invoke(
        DisableCustomCalendarEventSummaryTemplatesCommand $command)
    {
        $this->clearCustomCollection($command->getEventID());
    }




}
