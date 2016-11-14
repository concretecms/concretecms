<?php
namespace Concrete\Job;

use CollectionAttributeKey;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\File\File;
use Concrete\Core\Job\QueueableJob;
use Concrete\Core\Page\Page;
use Concrete\Core\Search\Index\IndexManagerInterface;
use Concrete\Core\User\User;
use FileAttributeKey;
use Loader;
use UserAttributeKey;
use ZendQueue\Message as ZendQueueMessage;
use ZendQueue\Queue as ZendQueue;

class IndexSearchAll extends QueueableJob
{

    // A flag for clearing the index
    const CLEAR = "-1";

    public $jNotUninstallable = 1;
    public $jSupportsQueue = true;

    protected $usersIndexed = 0;
    protected $pagesIndexed = 0;
    protected $filesIndexed = 0;
    protected $sitesIndexed = 0;

    /*
     * @var \Concrete\Core\Search\Index\IndexManagerInterface
     */
    protected $indexManager;

    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    public function getJobName()
    {
        return t("Index Search Engine - All");
    }

    public function getJobDescription()
    {
        return t("Empties the page search index and reindexes all pages.");
    }

    public function __construct(IndexManagerInterface $indexManager, Connection $connection)
    {
        $this->indexManager = $indexManager;
        $this->connection = $connection;
    }

    public function start(ZendQueue $queue)
    {
        // Send a "clear" queue item to clear out the index
        $queue->send(self::CLEAR);

        // Queue everything
        foreach ($this->queueMessages() as $message) {
            $queue->send($message);
        }
    }

    /**
     * Messages to add to the queue
     * @return \Iterator
     */
    protected function queueMessages()
    {
        foreach ($this->pagesToQueue() as $id) {
            yield "P{$id}";
        }
        foreach ($this->usersToQueue() as $id) {
            yield "U($id}";
        }
        foreach ($this->filesToQueue() as $id) {
            yield "F{$id}";
        }
        foreach ($this->sitesToQueue() as $id) {
            yield "S{$id}";
        }
    }

    public function processQueueItem(ZendQueueMessage $msg)
    {
        $index = $this->indexManager;

        // Handle a "clear" message
        if ($msg->body == self::CLEAR) {
            $this->clearIndex($index);
        } else {
            $message = substr($msg->body, 1);
            $type = substr($msg->body, 0, 1);

            switch ($type) {
                case 'P':
                    $this->pagesIndexed++;
                    return $index->index(Page::class, $message);
                case 'U':
                    $this->usersIndexed++;
                    return $index->index(User::class, $message);
                case 'F':
                    $this->filesIndexed++;
                    return $index->index(File::class, $message);
                case 'S':
                    $this->sitesIndexed++;
                    return $index->index(Site::class, $message);
            }
        }
    }

    public function finish(ZendQueue $q)
    {
        return t(
            'Indexed %s Pages, %s Users, %s Files, and %s Sites.',
            $this->pagesIndexed,
            $this->usersIndexed,
            $this->filesIndexed,
            $this->sitesIndexed
        );
    }

    /**
     * Clear out all indexes
     * @param $index
     */
    protected function clearIndex($index)
    {
        $index->clear(Page::class);
        $index->clear(User::class);
        $index->clear(File::class);
        $index->clear(Site::class);
    }

    /**
     * Get Pages to add to the queue
     * @return \Iterator
     */
    protected function pagesToQueue()
    {
        $qb = $this->connection->createQueryBuilder();

        // Find all pages that need indexing
        $query = $qb
            ->select('p.cID')
            ->from('Pages', 'p')
            ->leftJoin('p', 'CollectionSearchIndexAttributes', 'a', 'p.cID = a.cID')
            ->where('cIsActive = 1')
            ->andWhere($qb->expr()->orX(
                'a.ak_exclude_search_index is null',
                'a.ak_exclude_search_index = 0'
            ))->execute();

        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }

    /**
     * Get Users to add to the queue
     * @return \Iterator
     */
    protected function usersToQueue()
    {
        /** @var Connection $db */
        $db = $this->connection;

        $query = $db->executeQuery('SELECT uID FROM Users WHERE uIsActive = 1');
        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }

    /**
     * Get Files to add to the queue
     * @return \Iterator
     */
    protected function filesToQueue()
    {
        /** @var Connection $db */
        $db = $this->connection;

        $query = $db->executeQuery('SELECT fID FROM Files');
        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }

    /**
     * Get Sites to add to the queue
     * @return \Iterator
     */
    protected function sitesToQueue()
    {
        /** @var Connection $db */
        $db = $this->connection;

        $query = $db->executeQuery('SELECT siteID FROM Sites');
        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }

}
