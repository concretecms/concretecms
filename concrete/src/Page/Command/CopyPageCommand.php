<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface;

class CopyPageCommand extends PageCommand implements BatchableCommandInterface
{
    /**
     * @var int
     */
    protected $destinationPageID;

    /**
     * @var bool
     */
    protected $isMultilingual;

    public function __construct(int $pageID, int $destinationPageID, bool $isMultilingual = false)
    {
        parent::__construct($pageID);
        $this->setDestinationPageID($destinationPageID);
        $this->isMultilingual = $isMultilingual;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Queue\Batch\Command\BatchableCommandInterface::getBatchHandle()
     */
    public function getBatchHandle(): string
    {
        return 'copy_page';
    }

    public function getDestinationPageID(): int
    {
        return $this->destinationPageID;
    }

    /**
     * @return $this
     */
    public function setDestinationPageID(int $destinationPageID): object
    {
        $this->destinationPageID = $destinationPageID;

        return $this;
    }

    public function isMultilingualCopy(): bool
    {
        return $this->isMultilingual;
    }
}
