<?php

namespace Concrete\Core\User\Group;

use Concrete\Core\Cache\Level\RequestCache;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\User\Group\Group;
use Concrete\Core\Tree\Node\Type\Group as GroupNode;
/**
 * This class simply exists as an intermediary between the local group repository and the Group::getByPath() method.
 * This will enable us to test the local group repository class.
 * @codeCoverageIgnore
 */
class GroupRepository
{

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var RequestCache
     */
    protected $cache;

    /**
     * GroupRepository constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection, RequestCache $cache)
    {
        $this->connection = $connection;
        $this->cache = $cache;
    }

    /**
     * @param $path
     * @return Group|null
     */
    public function getGroupByPath($path)
    {
        $table = $this->connection->getDatabasePlatform()->quoteSingleIdentifier('Groups');
        $row = $this->connection->fetchAssoc(
            'select * from ' . $table . ' where gPath = ?', [$path]
        );
        if ($row) {
            $g = new Group();
            $g->setPropertiesFromArray($row);
            return $g;
        }
    }

    /**
     * @param $name
     * @return Group|null
     */
    public function getGroupByName($name)
    {
        $table = $this->connection->getDatabasePlatform()->quoteSingleIdentifier('Groups');
        $row = $this->connection->fetchAssoc(
            'select * from ' . $table . ' where gName = ?', [$name]
        );
        if ($row) {
            $g = new Group();
            $g->setPropertiesFromArray($row);
            return $g;
        }

    }

    /**
     * @param $gID
     * @return Group|null
     */
    public function getGroupById($gID)
    {
        $identifier = sprintf('group/%s', $gID);
        $item = $this->cache->getItem($identifier);
        if (!$item->isMiss()) {
            return $item->get();
        }
        $table = $this->connection->getDatabasePlatform()->quoteSingleIdentifier('Groups');
        $row = $this->connection->fetchAssoc(
            'select * from ' . $table . ' where gID = ?', [$gID]
        );
        if ($row) {
            $g = new Group();
            $g->setPropertiesFromArray($row);
            $this->cache->save($item->set($g));
            return $g;
        }
    }

}
