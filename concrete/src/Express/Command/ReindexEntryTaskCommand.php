<?php

namespace Concrete\Core\Express\Command;

use Concrete\Core\Foundation\Command\Command;

class ReindexEntryTaskCommand extends Command
{

    /**
     * @var integer
     */
    protected $entryId;

    /**
     * @param int $entryId
     */
    public function __construct(int $entryId)
    {
        $this->entryId = $entryId;
    }

    /**
     * @return int
     */
    public function getEntryId(): int
    {
        return $this->entryId;
    }




}
