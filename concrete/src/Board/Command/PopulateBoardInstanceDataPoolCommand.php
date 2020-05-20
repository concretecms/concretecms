<?php

namespace Concrete\Core\Board\Command;

class PopulateBoardInstanceDataPoolCommand
{

    use BoardInstanceTrait;

    /**
     * The unix timestamp to get all data since. Used to update progressively.
     *
     * @var int
     */
    protected $retrieveDataObjectsAfter = 0;

    /**
     * @return int
     */
    public function getRetrieveDataObjectsAfter(): int
    {
        return $this->retrieveDataObjectsAfter;
    }

    /**
     * @param int $retrieveDataObjectsAfter
     */
    public function setRetrieveDataObjectsAfter(int $retrieveDataObjectsAfter): void
    {
        $this->retrieveDataObjectsAfter = $retrieveDataObjectsAfter;
    }




}
