<?php

namespace Concrete\Core\Command\Batch;

use Concrete\Core\Entity\Command\Batch;

trait BatchAwareTrait
{

    /**
     * @var Batch
     */
    protected $batch;

    public function setBatch(Batch $batch)
    {
        $this->batch = $batch;
    }

    public function getBatch()
    {
        return $this->batch;
    }

}
