<?php

namespace Concrete\Core\Calendar\Event\Summary\Template\Command;

use Concrete\Core\Calendar\Event\Command\CalendarEventCommand;

class EnableCustomCalendarEventSummaryTemplatesCommand extends CalendarEventCommand
{

    protected $templateIDs = [];

    /**
     * @return array
     */
    public function getTemplateIDs(): array
    {
        return $this->templateIDs;
    }

    /**
     * @param array $templateIDs
     */
    public function setTemplateIDs(array $templateIDs): void
    {
        $this->templateIDs = $templateIDs;
    }



}
