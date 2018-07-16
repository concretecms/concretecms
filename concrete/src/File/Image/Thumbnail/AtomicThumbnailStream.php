<?php

namespace Concrete\Core\File\Image\Thumbnail;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\DatabaseManager;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class AtomicThumbnailStream
 * Outputs a stream of thumbnails in an atomic way. This ensures a thumbnail will only be built on a single thread at
 * a time
 */
class AtomicThumbnailStream implements \IteratorAggregate
{

    /** @var \Concrete\Core\Database\DatabaseManager */
    protected $manager;

    /** @var string */
    protected $table = 'FileImageThumbnailPaths';

    /** @var string */
    protected $lockColumn = 'lockID';

    /** @var string */
    protected $lockTimeoutColumn = 'lockExpires';

    /** @var string */
    protected $isBuiltColumn = 'isBuilt';

    /** @var int */
    protected $timeout = 10;

    /** @var string */
    protected $lockID = null;

    public function __construct(DatabaseManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Get an iterator that outputs locked thumbnail rows
     * @return \Generator|void
     */
    public function getIterator()
    {
        $db = $this->manager->connection();
        // Make sure we have the proper columns before doing anything with SQL
        if (!$this->lockColumnsExist($db)) {
            return;
        }

        $qb = $db->createQueryBuilder();
        $nextQuery = $this->getNextQuery($qb);
        $try = 10;

        while ($next = $this->next($nextQuery)) {
            unset($next[$this->lockColumn]);
            unset($next[$this->lockTimeoutColumn]);
            if (!$this->reserveNext($next, $db)) {
                if ($try--) {
                    // Avoid infinite loop
                    break;
                }

                // We were unable to reserve this properly, lets try the next item instead.
                continue;
            }

            yield $next;
        }
    }

    /**
     * Get the next matching thumbnail path row
     * @param \Doctrine\DBAL\Query\QueryBuilder $nextQuery
     * @return mixed
     */
    private function next(QueryBuilder $nextQuery)
    {
        $now = new \DateTime('now');

        $qb = clone $nextQuery;
        $qb->setParameter(':currentTime', $now->format('Y-m-d H:i:s'));

        return $qb->execute()->fetch();
    }

    /**
     * Mark the next item as reserved
     * @param array $next
     * @param \Concrete\Core\Database\Connection\Connection $db
     * @return bool
     */
    private function reserveNext(array $next, Connection $db)
    {
        $lockID = $this->getLockID();

        // First update the table
        try {
            $date = new \DateTime('now');
            $date->setTimestamp($date->getTimestamp() + $this->timeout);

            if (!$db->update($this->table, [
                $this->lockColumn => $lockID,
                $this->lockTimeoutColumn => $date->format('Y-m-d H:i:s')
            ], $next)
            ) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return $this->matchingLock($lockID, $next, $db);
    }

    /**
     * Find the row matching the passed criteria
     * This is used to verify that we've successfully locked a row in the database.
     * @param $lockID
     * @param array $next
     * @param \Concrete\Core\Database\Connection\Connection $db
     * @return bool|mixed
     */
    private function matchingLock($lockID, array $next, Connection $db)
    {
        // Then go back and double check that we successfully locked it
        $qb = $db->createQueryBuilder();

        $select = $qb
            ->select('*')
            ->from($this->table, 't')
            // Match the lock ID we just tried to set
            ->where($qb->expr()->eq($this->lockColumn, $qb->expr()->literal($lockID)));

        // Build additional predicates from already known values
        $predicates = array_map(function ($value, $key) use ($qb) {
            return $qb->expr()->eq($key, $qb->expr()->literal($value));
        }, $next, array_keys($next));

        call_user_func_array([$select, 'andWhere'], $predicates);

        // Get the result and validate it
        if ($result = $select->setMaxResults(1)->execute()->fetch()) {
            return $result;
        }

        return false;
    }

    /**
     * Check if the required lock columns have been added to the database.
     * This prevents people upgrading from seeing 500 errors in their logs.
     *
     * @param \Concrete\Core\Database\Connection\Connection $db
     * @return bool
     */
    private function lockColumnsExist(Connection $db)
    {
        $schema = $db->getSchemaManager()->listTableColumns($this->table);
        return isset($schema['lockid']);
    }

    /**
     * Set the timeout for the table lock in seconds
     * @param string $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = (int)$timeout;
    }

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $qb
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    protected function getNextQuery(QueryBuilder $qb)
    {
        $qb = clone $qb;
        return $qb->select('*')
            ->from($this->table, 't')
            ->where($qb->expr()->orX(
                $qb->expr()->isNull($this->lockTimeoutColumn),
                $qb->expr()->lte($this->lockTimeoutColumn, ':currentTime')
            ))
            ->andWhere($qb->expr()->neq($this->isBuiltColumn, '1'))
            ->setMaxResults(1);
    }

    /**
     * Get and memoize the current lock ID
     * @return string
     */
    private function getLockID()
    {
        if ($this->lockID) {
            return $this->lockID;
        }

        $id = uniqid('thumbnail_thread_', true);

        $this->lockID = $id;
        return $id;
    }

}
