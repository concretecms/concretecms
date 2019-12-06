<?php

namespace Concrete\Core\Board\Command;

class EnableCustomSlotTemplatesCommand extends BoardCommand
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
