<?php

namespace Concrete\Core\File\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\File\File;

class ClearFileIndexCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @var Connection
     */
    protected $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function __invoke(ClearFileIndexCommand $command)
    {
        $this->output->write(t('Clearing file index...'));
        if ($this->db->tableExists('FileSearchIndexAttributes')) {
            $this->db->executeUpdate('truncate table FileSearchIndexAttributes');
        }
    }

}
