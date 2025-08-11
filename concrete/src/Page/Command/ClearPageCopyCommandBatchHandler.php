<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Database\Connection\Connection;

class ClearPageCopyCommandBatchHandler
{

    /**
     * @var Connection
     */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function __invoke(ClearPageCopyCommandBatch $command)
    {
        $this->db->delete('QueuePageDuplicationRelations', ['queue_name' => $command->getCopyBatchID()]);
    }


}