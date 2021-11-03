<?php

namespace Concrete\Core\Express\Command;

use Concrete\Core\Foundation\Command\Command;

class ClearEntityIndexCommand extends Command
{

    /**
     * @var string
     */
    protected $entityId;

    /**
     * ClearEntityIndexCommand constructor.
     * @param string $entityId
     */
    public function __construct(string $entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * @return string
     */
    public function getEntityId(): string
    {
        return $this->entityId;
    }



}
