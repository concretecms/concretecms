<?php

namespace Concrete\Core\User\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\File;

class RemoveOldFileAttachmentsCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @param Connection $connection
     * @param Repository $config
     */
    public function __construct(Connection $connection, Repository $config)
    {
        $this->connection = $connection;
        $this->config = $config;
    }

    /**
     * @param ReindexUserTaskCommand $command
     */
    public function __invoke(RemoveOldFileAttachmentsCommand $command)
    {
        $removedItemsCounter = 0;
        $attachmentsLifetime = (int) $this->config->get("concrete.private_messages.attachments_lifetime", 14);

        $sql = <<<SQL
    SELECT 
        attachments.* 
    FROM 
        UserPrivateMessages AS msg 
    JOIN 
        UserPrivateMessagesAttachments AS attachments ON (msg.msgID = attachments.msgID) 
    WHERE 
        msg.msgDateCreated < NOW() - INTERVAL %s DAY;
SQL;

        foreach ($this->connection->fetchAllAssociative(sprintf($sql, $attachmentsLifetime)) as $row) {
            $file = File::getByID($row["fID"]);

            if ($file instanceof \Concrete\Core\Entity\File\File) {
                $file->delete();
            }

            $this->connection->executeQuery("DELETE FROM UserPrivateMessagesAttachments WHERE msgID = ? AND fID = ?", [$row["msgID"], $row["fID"]]);

            $removedItemsCounter++;
        }

        $this->output->write(t2('%s attachment removed','%s attachments removed', $removedItemsCounter));

    }


}