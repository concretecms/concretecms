<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Command\CommandInterface;

abstract class PageCommand implements CommandInterface
{

    protected $pageID;

    /**
     * FileCommand constructor.
     * @param $fID
     */
    public function __construct($pageID)
    {
        $this->pageID = $pageID;
    }

    /**
     * @return mixed
     */
    public function getPageID()
    {
        return $this->pageID;
    }



}