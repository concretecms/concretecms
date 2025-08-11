<?php

namespace Concrete\Core\Page\Command;

class CopyPageCommand extends PageCommand
{
    /**
     * @var int
     */
    protected $destinationPageID;

    /**
     * @var bool
     */
    protected $multilingualCopy;

    /**
     * @var string
     */
    protected $copyBatchID;

    public function __construct(int $pageID, string $copyBatchID, int $destinationPageID, bool $multilingualCopy = false)
    {
        parent::__construct($pageID);
        $this->setDestinationPageID($destinationPageID);
        $this->copyBatchID = $copyBatchID;
        $this->multilingualCopy = $multilingualCopy;
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
        return $this->multilingualCopy;
    }

    /**
     * @return string
     */
    public function getCopyBatchID(): string
    {
        return $this->copyBatchID;
    }


}
