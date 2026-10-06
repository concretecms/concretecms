<?php

declare(strict_types=1);

namespace Concrete\Core\Conversation\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\Set\Set as FileSet;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class DeleteOrphanedConversationAttachmentsCommandHandler implements OutputAwareInterface
{
    use OutputAwareTrait;

    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(Connection $connection, Repository $config, EntityManagerInterface $entityManager)
    {
        $this->connection = $connection;
        $this->config = $config;
        $this->entityManager = $entityManager;
    }

    public function __invoke(DeleteOrphanedConversationAttachmentsCommand $command)
    {
        $removedItemsCounter = 0;
        $minAge = $command->getMinAge();
        if ($minAge === null) {
            $minAge = (int) $this->config->get('conversations.attachments_orphaned_min_age', 28800);
        }
        foreach ($this->getOrphanedFileIDs(max(0, $minAge), $command->getLimit()) as $fileID) {
            $file = $this->entityManager->find(File::class, $fileID);
            if ($file !== null) {
                $file->delete();
                $removedItemsCounter++;
            }
        }

        $this->output->write(t2('%s attachment removed', '%s attachments removed', $removedItemsCounter));
    }

    /**
     * Get the IDs of the files that have been uploaded to a conversation but that haven't been attached to a message.
     *
     * @param int $minAge the number of seconds after which the uploaded files are considered orphaned
     * @param int|null $limit the maximum number of files to be returned (NULL for no limit)
     *
     * @return int[]
     */
    protected function getOrphanedFileIDs(int $minAge, ?int $limit = null): array
    {
        $fileSetName = (string) $this->config->get('conversations.attachments_pending_file_set');
        $fileSet = $fileSetName === '' ? null : FileSet::getByName($fileSetName);
        if ($fileSet === null) {
            return [];
        }
        $sql = <<<'SQL'
        SELECT DISTINCT
            FileSetFiles.fID
        FROM
            FileSetFiles
        LEFT JOIN
            ConversationMessageAttachments ON FileSetFiles.fID = ConversationMessageAttachments.fID
        WHERE
            FileSetFiles.fsID = :fsID
            AND FileSetFiles.timestamp < DATE_SUB(NOW(), INTERVAL :minAge SECOND)
            AND ConversationMessageAttachments.fID IS NULL
        ORDER BY
            FileSetFiles.fID
        SQL;
        if ($limit !== null) {
            $sql .= ' LIMIT ' . $limit;
        }

        return array_map(
            'intval',
            $this->connection->fetchFirstColumn(
                $sql,
                ['fsID' => (int) $fileSet->getFileSetID(), 'minAge' => $minAge],
                ['fsID' => ParameterType::INTEGER, 'minAge' => ParameterType::INTEGER]
            )
        );
    }
}
