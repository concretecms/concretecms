<?php
namespace Concrete\Job;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\File\File;
use Concrete\Core\Job\QueueableJob;
use Concrete\Core\Page\Page;
use Concrete\Core\Search\Index\IndexManagerInterface;
use Concrete\Core\User\User;
use Punic\Misc as PunicMisc;
use ZendQueue\Message as ZendQueueMessage;
use ZendQueue\Queue as ZendQueue;

class IndexSearchAll extends QueueableJob
{
    // A flag for clearing the index
    const CLEAR = '-1';

    public $jNotUninstallable = 1;
    public $jSupportsQueue = true;

    protected $usersIndexed = 0;
    protected $pagesIndexed = 0;
    protected $filesIndexed = 0;
    protected $sitesIndexed = 0;

    protected $clearTable = true;

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
        return t('Index Search Engine - All');
    }

    public function getJobDescription()
    {
        return t('Empties the page search index and reindexes all pages.');
    }

    public function __construct(IndexManagerInterface $indexManager, Connection $connection)
    {
        $this->indexManager = $indexManager;
        $this->connection = $connection;
    }

    public function start(ZendQueue $queue)
    {
        if ($this->clearTable) {
            // Send a "clear" queue item to clear out the index
            $queue->send(self::CLEAR);
        }

        // Queue everything
        foreach ($this->queueMessages() as $message) {
            $queue->send($message);
        }
    }

    /**
     * Messages to add to the queue.
     *
     * @return \Iterator
     */
    protected function queueMessages()
    {
        foreach ($this->pagesToQueue() as $id) {
            yield "P{$id}";
        }
        foreach ($this->usersToQueue() as $id) {
            yield "U{$id}";
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
                    $index->index(Page::class, $message);
                    break;
                case 'U':
                    $this->usersIndexed++;
                    $index->index(User::class, $message);
                    break;
                case 'F':
                    $this->filesIndexed++;
                    $index->index(File::class, $message);
                    break;
                case 'S':
                    $this->sitesIndexed++;
                    $index->index(Site::class, $message);
                    break;
            }
        }
    }

    public function finish(ZendQueue $q)
    {
        return t(
            'Index performed on: %s',
            PunicMisc::join([
                t2('%d page', '%d pages', $this->pagesIndexed),
                t2('%d user', '%d users', $this->usersIndexed),
                t2('%d file', '%d files', $this->filesIndexed),
                t2('%d site', '%d sites', $this->sitesIndexed),
            ])
        );
    }

    /**
     * Clear out all indexes.
     *
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
     * Get Pages to add to the queue.
     *
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
     * Get Users to add to the queue.
     *
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
     * Get Files to add to the queue.
     *
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
     * Get Sites to add to the queue.
     *
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
