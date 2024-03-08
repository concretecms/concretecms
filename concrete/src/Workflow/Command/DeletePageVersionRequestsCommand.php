<?php

namespace Concrete\Core\Workflow\Command;

use Concrete\Core\Page\Command\PageCommand;

class DeletePageVersionRequestsCommand extends PageCommand
{
    protected int $versionID;

    public function __construct(int $pageID, int $versionID)
    {
        parent::__construct($pageID);
        $this->setVersionID($versionID);
    }

    public function getVersionID(): int
    {
        return $this->versionID;
    }

    public function setVersionID(int $versionID): self
    {
        $this->versionID = $versionID;

        return $this;
    }
}
