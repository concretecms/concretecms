<?php
namespace Concrete\Job;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use ZendQueue\Message;

class IndexSearch extends IndexSearchAll implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    protected $clearTable = false;

    public function getJobName()
    {
        return t("Index Search Engine - Updates");
    }

    public function getJobDescription()
    {
        return t(
            "Index the site to allow searching to work quickly and accurately"
        );
    }

    protected function queueMessages()
    {
        $generator = parent::queueMessages();

        foreach ($generator as $item) {
            yield $item;
        }

        foreach ($this->pagesToRemove() as $id) {
            yield "RP{$id}";
        }
        foreach ($this->usersToRemove() as $id) {
            yield "RU{$id}";
        }
        foreach ($this->filesToRemove() as $id) {
            yield "RF{$id}";
        }
        foreach ($this->sitesToRemove() as $id) {
            yield "RS{$id}";
        }
    }


    public function processQueueItem(Message $msg)
    {
        $index = $this->indexManager;

        parent::processQueueItem($msg);

        $message = substr($msg->body, 2);
        $remove = substr($msg->body, 0, 1);
        $type = substr($msg->body, 1, 1);

        // Make sure that we were meant to remove this item
        if ($remove !== 'R') {
            return;
        }

        switch ($type) {
            case 'P':
                $index->forget(Page::class, $message);
                break;
            case 'U':
                $index->forget(User::class, $message);
                break;
            case 'F':
                $index->forget(File::class, $message);
                break;
            case 'S':
                $index->forget(Site::class, $message);
                break;
        }
    }

    /**
     * Get Pages to add to the queue
     * @return \Iterator
     */
    protected function pagesToQueue()
    {
        $qb = $this->connection->createQueryBuilder();
        $timeout = $this->app['config']->get('concrete.misc.page_search_index_lifetime');

        //'( or psi.cID is null or psi.cDateLastIndexed is null)'
        $statement = $qb->select('p.cID')
            ->from('Pages', 'p')
            ->leftJoin('p', 'Collections', 'c', 'p.cID = c.cID')
            ->leftJoin('p', 'PageSearchIndex', 's', 'p.cID = s.cID')
            ->where('c.cDateModified > s.cDateLastIndexed')
            ->orWhere('UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(s.cDateLastIndexed) > ' . $timeout)
            ->orWhere('s.cID is null')
            ->orWhere('s.cDateLastIndexed is null')->execute();

        while ($id = $statement->fetchColumn()) {
            yield $id;
        }
    }

    /**
     * Get a list of sites to remove from the search index
     *
     * @return int[]
     */
    protected function sitesToRemove()
    {
        return [];
    }

    /**
     * Get a list of files to remove from the search index
     * @return int[]
     */
    protected function filesToRemove()
    {
        return [];
    }

    /**
     * Get a list of users to remove from the search index
     *
     * @return int[]
     */
    protected function usersToRemove()
    {
        return [];
    }

    /**
     * Get a list of pages to be removed from the search index
     *
     * @return int[];
     */
    protected function pagesToRemove()
    {
        $qb = $this->connection->createQueryBuilder();

        // Find all pages that need to be removed from the index
        $query = $qb
            ->select('p.cID')
            ->from('Pages', 'p')
            ->leftJoin('p', 'CollectionSearchIndexAttributes', 'a', 'p.cID = a.cID')
            ->leftJoin('p', 'PageSearchIndex', 's', 'p.cID = s.cID')
            ->where('s.cDateLastIndexed IS NOT NULL')
            ->andWhere($qb->expr()->orX(
                'p.cIsActive != 1',
                $qb->expr()->andX(
                    'a.ak_exclude_search_index IS NOT NULL',
                    'a.ak_exclude_search_index != 0'
                )
            ))->execute();

        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }

}
