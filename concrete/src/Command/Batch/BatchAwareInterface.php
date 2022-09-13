<?php
namespace Concrete\Core\Command\Batch;

use Concrete\Core\Entity\Command\Batch;

interface BatchAwareInterface
{

    public function setBatch(Batch $batch);

}
