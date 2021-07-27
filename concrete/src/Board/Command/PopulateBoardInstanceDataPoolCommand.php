<?php

namespace Concrete\Core\Board\Command;

use Concrete\Core\Foundation\Command\Command;

class PopulateBoardInstanceDataPoolCommand extends Command
{

    use BoardInstanceTrait;

    /**
     * CURRENTLY NOT USED
     *
     * The unix timestamp to get all data since. Used to update progressively.
     * If set to -1 then we dynamically determine this at runtime.
     *
     * @var int
     */
    protected $retrieveDataObjectsAfter = -1;

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
