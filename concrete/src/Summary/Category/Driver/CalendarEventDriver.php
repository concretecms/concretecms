<?php

namespace Concrete\Core\Summary\Category\Driver;

use Concrete\Core\Calendar\Event\EventOccurrenceService;
use Concrete\Core\Calendar\Event\EventService;
use Concrete\Core\Entity\Calendar\Summary\CalendarEventTemplate;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Template\RenderableTemplateInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class CalendarEventDriver extends AbstractDriver
{

    public function getCategoryMemberFromIdentifier($identifier): ?CategoryMemberInterface
    {
        return $this->app->make(EventOccurrenceService::class)->getByID($identifier);
    }

    public function getMemberSummaryTemplate($templateID): ?RenderableTemplateInterface
    {
        return $this->entityManager->find(CalendarEventTemplate::class, $templateID);
    }


}
