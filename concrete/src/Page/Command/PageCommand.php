<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Command\Command;

abstract class PageCommand extends Command
{
    /**
     * @var int
     */
    protected $pageID;

    public function __construct(int $pageID)
    {
        $this->pageID = $pageID;
    }

    public function getPageID(): int
    {
        return $this->pageID;
    }
}
