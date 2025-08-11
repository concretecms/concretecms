<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Foundation\Command\Command;

class ClearPageCopyCommandBatch extends Command
{

    /**
     * @var string
     */
    protected $copyBatchID;

    public function __construct(string $copyBatchID)
    {
        $this->copyBatchID = $copyBatchID;
    }

    /**
     * @return string
     */
    public function getCopyBatchID(): string
    {
        return $this->copyBatchID;
    }


}
