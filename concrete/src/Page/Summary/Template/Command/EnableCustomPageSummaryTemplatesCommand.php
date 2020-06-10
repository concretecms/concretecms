<?php

namespace Concrete\Core\Page\Summary\Template\Command;

use Concrete\Core\Page\Command\PageCommand;

class EnableCustomPageSummaryTemplatesCommand extends PageCommand
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
