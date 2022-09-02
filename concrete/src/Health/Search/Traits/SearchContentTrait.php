<?php

namespace Concrete\Core\Health\Search\Traits;

use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Value\Value\AbstractValue;
use Concrete\Core\Entity\Attribute\Value\Value\Value;
use Concrete\Core\Entity\Health\Report\Finding;
use Concrete\Core\Entity\Health\Report\SearchResult;
use Concrete\Core\Health\Report\Runner;
use Doctrine\DBAL\Query\QueryBuilder as DbalBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder as OrmBuilder;
use Concrete\Core\Health\Search\SearchMap;

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
     * @param callable $findingFactory fn($item, $report): Finding
     *
     * @return \Generator|Finding[]
     */
    protected function auditDbal(
        Runner $report,
        string $table,
        string $idSelect,
        array $scanColumns,
        array $joins,
        callable $findingFactory
    ): iterable {
        $qb = $this->em->getConnection()->createQueryBuilder();
        $qb->select($idSelect)->from($table, $table);

        if (!$this->applyQueryFilters($report, $qb)) {
            return;
        }

        // Add column select, if there are more than one columns just concat them together.
        if (count($scanColumns) > 1) {
            $columns = collect($scanColumns)->map(fn($c) => "ifnull($c,'')")->implode(',');
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
            $result = $findingFactory($item, $report);

            if ($result instanceof Finding) {
                yield $result;
            }
        }
    }

    /**
     * Convert a category string to a Finding constant value
     *
     * @param string $category
     * @return int
     */
    protected function categoryToType(string $category): int
    {
        return SearchMap::$findingTypeMap[strtolower($category)] ?? SearchMap::TYPE_ATTRIBUTE_UNKNOWN;
    }

    /**
     * Convert a category int to a category string
     *
     * @param int $type
     *
     * @return string
     */
    protected function typeToCategory(int $type): string
    {
        $map = array_flip(SearchMap::$findingTypeMap);
        return $map[$type] ?? $map[SearchMap::TYPE_ATTRIBUTE_UNKNOWN];
    }

    /**
     * Converts a value instance to a type value
     *
     * @param $value
     * @return int|null
     */
    protected function valueToType($value): ?int
    {
        if ($value instanceof AbstractValue) {
            $value = $value->getGenericValue();
        }

        if (!$value instanceof Value) {
            return null;
        }

        $key = $value->getAttributeKey();
        if (!$key instanceof Key) {
            return null;
        }

        if ($key instanceof ExpressKey) {
            $category = 'express';
        } else {
            $category = $key->getAttributeKeyCategoryHandle();
        }

        return $this->categoryToType($category);
    }

    /**
     * Convert a type to something human readable
     *
     * @param int $type
     * @return mixed|string
     */
    protected function typeToHumanReadable(int $type)
    {
        $map = array_flip(
            [
                'block' => SearchMap::TYPE_BLOCK,
                'configuration' => SearchMap::TYPE_CONFIGURATION,
                'express attribute' => SearchMap::TYPE_ATTRIBUTE_EXPRESS,
                'collection attribute' => SearchMap::TYPE_ATTRIBUTE_PAGE,
                'user attribute' => SearchMap::TYPE_ATTRIBUTE_USER,
                'event attribute' => SearchMap::TYPE_ATTRIBUTE_EVENT,
                'file attribute' => SearchMap::TYPE_ATTRIBUTE_FILE,
                'site attribute' => SearchMap::TYPE_ATTRIBUTE_SITE,
                'site_type attribute' => SearchMap::TYPE_ATTRIBUTE_SITE_TYPE,
                'legacy attribute' => SearchMap::TYPE_ATTRIBUTE_LEGACY,
                'unknown attribute' => SearchMap::TYPE_ATTRIBUTE_UNKNOWN,
            ]
        );

        return $map[$type] ?? 'unknown';
    }


}
