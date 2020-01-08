<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class CopyPageCommand extends PageCommand implements BatchableCommandInterface
{

    protected $destinationPageID;

    protected $isMultilingual = false;
    /**
     * CopyPageCommand constructor.
     * @param $destinationPageID
     */
    public function __construct($pageID, $destinationPageID, $isMultilingual = false)
    {
        parent::__construct($pageID);
        $this->destinationPageID = $destinationPageID;
        $this->isMultilingual = $isMultilingual;
    }

    /**
     * @return boolean
     */
    public function isMultilingualCopy()
    {
        return $this->isMultilingual;
    }

    /**
     * @return mixed
     */
    public function getDestinationPageID()
    {
        return $this->destinationPageID;
    }

    /**
     * @param mixed $destinationPageID
     */
    public function setDestinationPageID($destinationPageID)
    {
        $this->destinationPageID = $destinationPageID;
    }


    public function getBatchHandle()
    {
        return 'copy_page';
    }

}