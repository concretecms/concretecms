<?php
namespace Concrete\Job;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\File\File;
use Concrete\Core\Job\JobQueue;
use Concrete\Core\Job\JobQueueMessage;
use Concrete\Core\Page\Page;
use Concrete\Core\User\User;
use Punic\Misc as PunicMisc;

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
        $pages = $pagesToRemove = $usersToRemove = $filesToRemove = $sitesToRemove = $expressEntriesToRemove = 0;

        foreach ($this->pagesToQueue() as $id) {
            yield "P{$id}";
            $pages++;
        }
        foreach ($this->pagesToRemove() as $id) {
            yield "RP{$id}";
            $pagesToRemove++;
        }
        foreach ($this->usersToRemove() as $id) {
            yield "RU{$id}";
            $usersToRemove++;
        }
        foreach ($this->filesToRemove() as $id) {
            yield "RF{$id}";
            $filesToRemove++;
        }
        foreach ($this->sitesToRemove() as $id) {
            yield "RS{$id}";
            $sitesToRemove++;
        }
        foreach ($this->expressEntriesToRemove() as $id) {
            yield "RE{$id}";
            $expressEntriesToRemove++;
        }

        // Yield the result very last
        yield 'R' . json_encode([$pages, $pagesToRemove, $usersToRemove, $filesToRemove, $sitesToRemove, $expressEntriesToRemove]);
    }


    public function processQueueItem(JobQueueMessage $msg)
    {
        $index = $this->indexManager;

        parent::processQueueItem($msg);

        $body = $msg->body;
        $message = substr($body, 2);
        $remove = $body[0];
        $type = $body[1];

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
            case 'E':
                $index->forget(Entry::class, $message);
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
        $timeout = intval($this->app['config']->get('concrete.misc.page_search_index_lifetime'));

        // Find all pages that need indexing
        $query = $qb
            ->select('p.cID')
            ->from('Pages', 'p')
            ->leftJoin('p', 'CollectionSearchIndexAttributes', 'a', 'p.cID = a.cID')
            ->leftJoin('p', 'Collections', 'c', 'p.cID = c.cID')
            ->leftJoin('p', 'PageSearchIndex', 's', 'p.cID = s.cID')
            ->where('cIsActive = 1')
            ->andWhere($qb->expr()->orX(
                'a.ak_exclude_search_index is null',
                'a.ak_exclude_search_index = 0'
            ))
            ->andWhere($qb->expr()->orX(
                'cDateModified > s.cDateLastIndexed',
                "(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(s.cDateLastIndexed) > {$timeout})",
                's.cID is null',
                's.cDateLastIndexed is null'
            ))->execute();

        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }

    public function finish(JobQueue $q)
    {
        if ($this->result) {
            list($pages, $pagesToRemove, $usersToRemove, $filesToRemove, $sitesToRemove, $expressEntriesToRemove) = $this->result;

            return t(
                'Index performed on: %s',
                PunicMisc::joinAnd([
                    t2('%d page', '%d pages', $pages),
                    t2('%d deleted page', '%d deleted pages', $pagesToRemove),
                    t2('%d deleted user', '%d deleted users', $usersToRemove),
                    t2('%d deleted file', '%d deleted files', $filesToRemove),
                    t2('%d deleted site', '%d deleted sites', $sitesToRemove),
                    t2('%d deleted express entry', '%d deleted express entries', $expressEntriesToRemove),
                ])
            );
        }

        return t('Indexed pages, users, files, sites and express data.');
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
     * Get a list of express entries to remove from the search index
     *
     * @return int[]
     */
    protected function expressEntriesToRemove()
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
