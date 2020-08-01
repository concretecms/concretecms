<?php

namespace Concrete\Core\Logging;

use Closure;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Concrete\Core\Search\ItemList\Pager\Manager\LogListPagerManager;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;
use Concrete\Core\Search\Pagination\PaginationProviderInterface;
use DateTime;
use Doctrine\DBAL\Query\QueryBuilder;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

class LogList extends ItemList implements PagerProviderInterface, PaginationProviderInterface
{

    /**
     * Whether this is a search using fulltext.
     */
    protected $isFulltextSearch = false;

    /**
     * Columns in this array can be sorted via the request.
     *
     * @var array
     */
    protected $autoSortColumns = ['l.logID', 'l.channel', 'l.time', 'l.message', 'l.level', 'l.uID'];

    /** @var Closure|integer|null */
    protected $permissionsChecker = -1;

    public function createQuery()
    {
        $this->query->select('l.*')
            ->from('Logs', 'l');
    }

    public function finalizeQuery(QueryBuilder $query)
    {
        return $query;
    }

    public function filterByKeywords($keywords, $exact = false)
    {
        if (strlen($keywords) > 0) {
            $this->isFulltextSearch = true;

            if ($exact) {
                $this->query->andWhere('l.message = :keywords');
                $this->query->setParameter('keywords', $keywords);
            } else {
                $this->query->andWhere(
                    $this->query->expr()->like('l.message', ':keywords')
                );
                $this->query->setParameter('keywords', '%' . $keywords . '%');
            }
        }
    }

    public function filterByIds($logIds)
    {
        $sql = '';

        $i = 0;

        foreach ($logIds as $logId) {
            $i++;
            $sql .= (strlen($sql) > 0 ? " OR " : "") . "l.logID = :log_id_" . $i;
            $this->query->setParameter('log_id_' . $i, $logId);
        }

        if (strlen($sql) > 0) {
            $sql = "(" . $sql . ")";
            $this->query->andWhere($sql);
        }
    }

    public function filterByChannel($channel)
    {
        $this->query->andWhere('l.channel = :channel')->setParameter('channel', $channel);
    }

    public function filterByLevels($levels)
    {
        if (is_array($levels)) {
            $lth = '(';
            for ($i = 0, $iMax = count($levels); $i < $iMax; ++$i) {
                if ($i > 0) {
                    $lth .= ',';
                }
                $lth .= $this->query->getConnection()->quote($levels[$i]);
            }
            $lth .= ')';
            $this->query->andWhere("(l.level in {$lth})");
        }
    }

    public function filterByStartTime($time)
    {
        if (is_string($time)) {
            $time = new DateTime($time);
        }

        $this->query->andWhere('l.time > :start_date')->setParameter('start_date', $time->format('Y-m-d H:i:s'));
    }

    public function filterByEndTime($time)
    {
        if (is_string($time)) {
            $time = new DateTime($time);
        }

        $this->query->andWhere('l.time < :end_date')->setParameter('end_date', $time->format('Y-m-d H:i:s'));
    }

    /**
     * @param array $queryRow
     * @return LogEntry
     */
    public function getResult($queryRow)
    {
        return new LogEntry($queryRow);
    }

    public function getTotalResults()
    {
        if ($this->permissionsChecker === -1) {
            return $this->deliverQueryObject()
                ->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct l.logID)')
                ->setMaxResults(1)
                ->execute()
                ->fetchColumn();
        }

        return -1; // unknown
    }

    function getPagerManager()
    {
        return new LogListPagerManager($this);
    }

    function getPagerVariableFactory()
    {
        return new VariableFactory($this, $this->getSearchRequest());
    }

    function getPaginationAdapter()
    {
        return new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])
                ->select('count(distinct l.logID)')
                ->setMaxResults(1);
        });
    }

    public function checkPermissions($mixed)
    {
        if (isset($this->permissionsChecker)) {
            if ($this->permissionsChecker === -1) {
                return true;
            }

            return call_user_func_array($this->permissionsChecker, [$mixed]);
        }

        return true;
    }

    public function setPermissionsChecker(Closure $checker = null)
    {
        $this->permissionsChecker = $checker;
    }

    public function ignorePermissions()
    {
        $this->permissionsChecker = -1;
    }

    public function getPermissionsChecker()
    {
        return $this->permissionsChecker;
    }

    public function enablePermissions()
    {
        unset($this->permissionsChecker);
    }

    public function isFulltextSearch()
    {
        return $this->isFulltextSearch;
    }
}
