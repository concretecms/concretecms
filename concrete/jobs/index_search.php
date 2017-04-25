<?php
namespace Concrete\Job;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;

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

}
