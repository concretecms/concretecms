<?php

namespace Concrete\Core\Command\Batch\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class BatchStamp implements StampInterface
{

    /**
     * @var string
     */
    protected $batchId;

    /**
     * @param string $batchId
     */
    public function __construct(string $batchId = null)
    {
        $this->batchId = $batchId;
    }

    /**
     * @return string
     */
    public function getBatchId(): string
    {
        return $this->batchId;
    }

    /**
     * @param string $batchId
     */
    public function setBatchId(string $batchId): void
    {
        $this->batchId = $batchId;
    }


}
