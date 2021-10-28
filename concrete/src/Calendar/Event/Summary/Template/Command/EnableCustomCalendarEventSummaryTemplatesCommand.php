<?php

namespace Concrete\Core\Calendar\Event\Summary\Template\Command;

use Concrete\Core\Calendar\Event\Command\CalendarEventCommand;

class EnableCustomCalendarEventSummaryTemplatesCommand extends CalendarEventCommand
{

    /**
     * @var int[]
     */
    protected $templateIDs = [];

    /**
     * @return int[]
     */
    public function getTemplateIDs(): array
    {
        return $this->templateIDs;
    }

    /**
     * @param int[] $templateIDs
     *
     * @return $this
     */
    public function setTemplateIDs(array $templateIDs): object
    {
        $this->templateIDs = $templateIDs;

        return $this;
    }
}
