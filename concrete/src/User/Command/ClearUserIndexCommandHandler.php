<?php

namespace Concrete\Core\User\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\Command\ClearFileIndexCommand;

class ClearUserIndexCommandHandler implements OutputAwareInterface
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

    public function __invoke(ClearUserIndexCommand $command)
    {
        $this->output->write(t('Clearing user index...'));
        if ($this->db->tableExists('UserSearchIndexAttributes')) {
            $this->db->executeUpdate('truncate table UserSearchIndexAttributes');
        }
    }

}
