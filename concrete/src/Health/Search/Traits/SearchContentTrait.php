<?php

namespace Concrete\Core\Health\Search\Traits;

use Concrete\Core\Block\Block;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Health\Report\SearchResult;
use Concrete\Core\Health\Report\Finding\Control\DropdownControl;
use Concrete\Core\Health\Report\Finding\Control\DropdownItemControl;
use Concrete\Core\Health\Report\Finding\Control\FindingDetailControl;
use Concrete\Core\Health\Report\Finding\Message\Search\BlockMessage;
use Concrete\Core\Health\Report\Runner;
use Doctrine\DBAL\Query\QueryBuilder as DbalBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder as OrmBuilder;

/**
 * Helpful search methods
 */
trait SearchContentTrait
{
    /**
     * Take a query object and add "or having" or "and having" for each filter on the Report
     * This object gives us one place to implement new types of filters added to the Report class
     *
     * @param Runner $report
     * @param OrmBuilder|DbalBuilder $query
     * @param bool $having
     * @param string $filterColumn
     *
     * @return bool
     */
    protected function applyQueryFilters(Runner $report, $query, bool $having = true, string $filterColumn = 'content')
    {
        /**
         * @var $result SearchResult
         */
        $result = $report->getResult();
        if (!$query instanceof OrmBuilder && !$query instanceof DbalBuilder) {
            throw new \InvalidArgumentException('Invalid query object provided.');
        }

        $keywords = [];
        $tags = [];
        if ($result->getSearchType() === $result::TYPE_TAG) {
            $tags = [$result->getSearchString()];
        } else {
            $keywords = [$result->getSearchString()];
        }

        if (!count($keywords) && !count($tags)) {
            return false;
        }

        $method = $having ? [$query, 'orHaving'] : [$query, 'orWhere'];

        for ($i = 0; $i < count($keywords); $i++) {
            $method("{$filterColumn} like :word{$i}")->setParameter(":word{$i}", "%{$keywords[$i]}%");
        }

        for ($i = 0; $i < count($tags); $i++) {
            $method("{$filterColumn} like :tag{$i}")->setParameter(":tag{$i}", "%<{$tags[$i]}%");
        }

        return true;
    }

    /**
     * Take a query and paginate over it in chunks defined by $perPage
     * You can iterate over the return value and expect to receive all rows one by one.
     *
     * @param $query
     * @param int $perPage
     *
     * @return \Generator
     */
    protected function iterateQuery($query, int $perPage = 1000): iterable
    {
        if (!$query instanceof Query && !$query instanceof \Doctrine\DBAL\Query\QueryBuilder) {
            throw new \InvalidArgumentException("Invalid ORM query provided.");
        }

        $cursor = 0;
        do {
            $total = 0;
            $query->setFirstResult($cursor);
            $query->setMaxResults($perPage);
            $resultSet = $query instanceof Query ? $query->iterate() : $query->execute();

            foreach ($resultSet as $item) {
                $total++;
                yield $query instanceof Query ? head($item) : $item;
            }

            $cursor += $perPage;
        } while ($total === $perPage);
    }

    /**
     * Audit a dbal table or set of tables
     *
     * @param Runner $report
     * @param string $table
     * @param string $idSelect
     * @param string[] $scanColumns
     * @param array[] $joins [[$joinTable, $fromTable, $alias, $criteria]]
     * @param callable $findingPopulator function($item, $report)
     *
     */
    protected function auditDbal(
        Runner $report,
        string $table,
        string $idSelect,
        array $scanColumns,
        array $joins,
        callable $findingPopulator
    ) {
        $db = app(Connection::class);
        $qb = $db->createQueryBuilder();
        $qb->select($idSelect)->from($table, $table);

        if (!$this->applyQueryFilters($report, $qb)) {
            return;
        }

        // Add column select, if there are more than one columns just concat them together.
        if (count($scanColumns) > 1) {
            $columns = collect($scanColumns)->map(function($c) { return "ifnull($c,'')"; })->implode(',');
            $qb->addSelect("concat({$columns}) as content");
        } else {
            $columns = head($scanColumns);
            $qb->addSelect("{$columns} as content");
        }

        // Add joins
        foreach ($joins as $criteria) {
            [$joinTable, $fromTable, $alias, $criteria] = $criteria;
            $qb->leftJoin($fromTable, $joinTable, $alias, $criteria);
        }

        // Iterate over the query and build findings to output
        foreach ($this->iterateQuery($qb) as $item) {
            $findingPopulator($item, $report);
        }
    }

    protected function addBlockWarning(Runner $report, Block $block, string $content = '')
    {
        $message = new BlockMessage($block->getBlockID(), $content);
        $formatter = $message->getFormatter();
        $location = $formatter->getLocation($message);
        $detailsControl = new FindingDetailControl();
        $report->warning($message, new DropdownControl([$detailsControl, new DropdownItemControl($location)]));
    }





}
