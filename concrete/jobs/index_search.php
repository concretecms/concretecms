<?php
namespace Concrete\Job;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;

class IndexSearch extends IndexSearchAll implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

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

}
