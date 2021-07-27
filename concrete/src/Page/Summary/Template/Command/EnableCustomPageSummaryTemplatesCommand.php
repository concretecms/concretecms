<?php

namespace Concrete\Core\Page\Summary\Template\Command;

use Concrete\Core\Page\Command\PageCommand;

class EnableCustomPageSummaryTemplatesCommand extends PageCommand
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
