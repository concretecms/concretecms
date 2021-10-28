<?php

namespace Concrete\Core\Search\Index;

use Concrete\Core\Database\Connection\Connection;

/**
 * Provides messages to the reindexer for common objects
 */
class IndexObjectProvider
{

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * IndexObjectProvider constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get Pages to add to the queue.
     *
     * @return \Iterator
     */
    public function fetchPages()
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
    public function fetchUsers()
    {
        /** @var Connection $db */
        $db = $this->connection;

        $query = $db->executeQuery('SELECT uID FROM Users WHERE uIsActive = 1');
        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }

    /**
     * Get Express objects to add to the queue.
     *
     * @return \Iterator
     */
    public function fetchExpressObjects()
    {
        /** @var Connection $db */
        $db = $this->connection;

        $query = $db->executeQuery('SELECT id FROM ExpressEntities order by id asc');
        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }

    /**
     * Get Express entries to add to the queue.
     *
     * @return \Iterator
     */
    public function fetchExpressEntries()
    {
        /** @var Connection $db */
        $db = $this->connection;

        $query = $db->executeQuery('SELECT exEntryID FROM ExpressEntityEntries order by exEntryID asc');
        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }


    /**
     * Get Files to add to the queue.
     *
     * @return \Iterator
     */
    public function fetchFiles()
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
    public function fetchSites()
    {
        /** @var Connection $db */
        $db = $this->connection;

        $query = $db->executeQuery('SELECT siteID FROM Sites');
        while ($id = $query->fetchColumn()) {
            yield $id;
        }
    }
}
