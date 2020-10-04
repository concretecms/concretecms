<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Foundation\Command\Command;

class EnableCustomSlotTemplatesCommand extends Command
{

    use BoardTrait;

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
