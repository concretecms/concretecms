<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Command\CommandInterface;

abstract class PageCommand implements CommandInterface
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
