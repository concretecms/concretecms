<?php

declare(strict_types=1);

namespace Concrete\Core\User\Search;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\User\Group\MembershipExpirationChecker;

defined('C5_EXECUTE') or die('Access Denied.');

final class MembershipsProvider
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var MembershipExpirationChecker
     */
    private $expirationChecker;

    /**
     * @var array
     */
    private $membershipsByUserID = [];

    /**
     * @var array
     */
    private $loadedUserIDs = [];

    /**
     * @var array
     */
    private $pathsByGroupID = [];

    /**
     * @var array
     */
    private $loadedPathGroupIDs = [];

    /**
     * @var array
     */
    private $treeNodesByID = [];

    /**
     * @var array
     */
    private $nodeIDByGroupID = [];

    public function __construct(Connection $connection, MembershipExpirationChecker $expirationChecker)
    {
        $this->connection = $connection;
        $this->expirationChecker = $expirationChecker;
    }

    /**
     * Preload all the memberships needed to render one page of user search results.
     *
     * @param int[] $userIDs
     */
    public function preload(array $userIDs, bool $includeGroupPaths = false): void
    {
        $userIDs = $this->normalizeIDs($userIDs);
        $missingUserIDs = [];
        foreach ($userIDs as $userID) {
            if (!isset($this->loadedUserIDs[$userID])) {
                $this->loadedUserIDs[$userID] = true;
                $this->membershipsByUserID[$userID] = [];
                $missingUserIDs[] = $userID;
            }
        }

        if ($missingUserIDs !== []) {
            $this->loadMemberships($missingUserIDs);
        }

        if ($includeGroupPaths) {
            $groupIDs = [];
            foreach ($userIDs as $userID) {
                foreach ($this->membershipsByUserID[$userID] as $membership) {
                    $groupIDs[] = $membership['id'];
                }
            }
            $this->preloadPaths($groupIDs);
        }
    }

    public function getDirectMembershipsCount(int $userID): int
    {
        $this->preload([$userID]);

        return count($this->membershipsByUserID[$userID] ?? []);
    }

    /**
     * @return string[]
     */
    public function getDirectGroupNames(int $userID): array
    {
        $this->preload([$userID]);

        return array_values(array_map(static function (array $membership): string {
            return $membership['name'];
        }, $this->membershipsByUserID[$userID] ?? []));
    }

    /**
     * Returns the full hierarchy path for each of the user's direct group memberships,
     * for example ['Region', 'Office', 'Editors']. When a direct group has no known
     * position in the group tree, the path falls back to just the group's own name.
     *
     * @return string[][]
     */
    public function getGroupMembershipPaths(int $userID): array
    {
        $this->preload([$userID], true);
        $paths = [];
        foreach ($this->membershipsByUserID[$userID] ?? [] as $membership) {
            $path = $this->pathsByGroupID[$membership['id']] ?? [];
            if ($path === []) {
                $path = [$membership['name']];
            }
            $paths[] = $path;
        }

        return $paths;
    }

    public function clear(): void
    {
        $this->membershipsByUserID = [];
        $this->loadedUserIDs = [];
        $this->pathsByGroupID = [];
        $this->loadedPathGroupIDs = [];
        $this->treeNodesByID = [];
        $this->nodeIDByGroupID = [];
    }

    /**
     * @param int[] $userIDs
     */
    private function loadMemberships(array $userIDs): void
    {
        $groupsTable = $this->connection->getDatabasePlatform()->quoteSingleIdentifier('Groups');
        $result = $this->connection->createQueryBuilder()
            ->select(
                'ug.uID',
                'ug.ugEntered',
                'g.gID',
                'g.gName',
                'g.gUserExpirationIsEnabled',
                'g.gUserExpirationMethod',
                'g.gUserExpirationSetDateTime',
                'g.gUserExpirationInterval'
            )
            ->from('UserGroups', 'ug')
            ->innerJoin('ug', $groupsTable, 'g', 'g.gID = ug.gID')
            ->where('ug.uID in (:userIDs)')
            ->andWhere('ug.gID > :minimumGroupID')
            ->setParameter('userIDs', $userIDs, Connection::PARAM_INT_ARRAY)
            ->setParameter('minimumGroupID', REGISTERED_GROUP_ID)
            ->execute()
        ;
        $now = time();
        while ($row = $result->fetchAssociative()) {
            if ($this->expirationChecker->isExpired(
                (bool) $row['gUserExpirationIsEnabled'],
                $row['gUserExpirationMethod'] === null ? null : (string) $row['gUserExpirationMethod'],
                $row['gUserExpirationSetDateTime'] === null ? null : (string) $row['gUserExpirationSetDateTime'],
                (int) $row['gUserExpirationInterval'],
                $row['ugEntered'] === null ? null : (string) $row['ugEntered'],
                $now
            )) {
                continue;
            }
            $userID = (int) $row['uID'];
            $groupID = (int) $row['gID'];
            $this->membershipsByUserID[$userID][$groupID] = [
                'id' => $groupID,
                'name' => (string) $row['gName'],
            ];
        }
    }

    /**
     * @param int[] $groupIDs
     */
    private function preloadPaths(array $groupIDs): void
    {
        $groupIDs = $this->normalizeIDs($groupIDs);
        $missingGroupIDs = [];
        foreach ($groupIDs as $groupID) {
            if (!isset($this->loadedPathGroupIDs[$groupID])) {
                $this->loadedPathGroupIDs[$groupID] = true;
                $this->pathsByGroupID[$groupID] = [];
                $missingGroupIDs[] = $groupID;
            }
        }
        if ($missingGroupIDs === []) {
            return;
        }

        $this->loadDirectGroupNodes($missingGroupIDs);
        $frontier = [];
        foreach ($missingGroupIDs as $groupID) {
            $nodeID = $this->nodeIDByGroupID[$groupID] ?? 0;
            if ($nodeID === 0) {
                continue;
            }
            $parentID = $this->treeNodesByID[$nodeID]['parentID'];
            if ($parentID > 0 && !isset($this->treeNodesByID[$parentID])) {
                $frontier[] = $parentID;
            }
        }

        while (($frontier = $this->normalizeIDs($frontier)) !== []) {
            $frontier = array_values(array_filter($frontier, function (int $nodeID): bool {
                return !isset($this->treeNodesByID[$nodeID]);
            }));
            if ($frontier === []) {
                break;
            }
            $frontier = $this->loadTreeNodes($frontier);
        }

        foreach ($missingGroupIDs as $groupID) {
            $nodeID = $this->nodeIDByGroupID[$groupID] ?? 0;
            if ($nodeID === 0) {
                continue;
            }
            $node = $this->treeNodesByID[$nodeID];
            $parentID = $node['parentID'];
            $parentNames = [];
            $visited = [];
            while ($parentID > 0 && isset($this->treeNodesByID[$parentID]) && !isset($visited[$parentID])) {
                $visited[$parentID] = true;
                $parent = $this->treeNodesByID[$parentID];
                if ($parent['groupName'] !== null) {
                    $parentNames[] = $parent['groupName'];
                }
                $parentID = $parent['parentID'];
            }
            if ($parentNames !== []) {
                $this->pathsByGroupID[$groupID] = array_merge(
                    array_reverse($parentNames),
                    [$node['groupName']]
                );
            }
        }
    }

    /**
     * @param int[] $groupIDs
     */
    private function loadDirectGroupNodes(array $groupIDs): void
    {
        $groupsTable = $this->connection->getDatabasePlatform()->quoteSingleIdentifier('Groups');
        $result = $this->connection->createQueryBuilder()
            ->select('tgn.gID', 'tn.treeNodeID', 'tn.treeNodeParentID', 'g.gName')
            ->from('TreeGroupNodes', 'tgn')
            ->innerJoin('tgn', 'TreeNodes', 'tn', 'tn.treeNodeID = tgn.treeNodeID')
            ->innerJoin('tgn', $groupsTable, 'g', 'g.gID = tgn.gID')
            ->where('tgn.gID in (:groupIDs)')
            ->setParameter('groupIDs', $groupIDs, Connection::PARAM_INT_ARRAY)
            ->execute()
        ;
        while ($row = $result->fetchAssociative()) {
            $groupID = (int) $row['gID'];
            $nodeID = (int) $row['treeNodeID'];
            $this->nodeIDByGroupID[$groupID] = $nodeID;
            $this->treeNodesByID[$nodeID] = [
                'parentID' => (int) $row['treeNodeParentID'],
                'groupName' => (string) $row['gName'],
            ];
        }
    }

    /**
     * @param int[] $nodeIDs
     *
     * @return int[]
     */
    private function loadTreeNodes(array $nodeIDs): array
    {
        $groupsTable = $this->connection->getDatabasePlatform()->quoteSingleIdentifier('Groups');
        $result = $this->connection->createQueryBuilder()
            ->select('tn.treeNodeID', 'tn.treeNodeParentID', 'g.gName')
            ->from('TreeNodes', 'tn')
            ->leftJoin('tn', 'TreeGroupNodes', 'tgn', 'tgn.treeNodeID = tn.treeNodeID')
            ->leftJoin('tgn', $groupsTable, 'g', 'g.gID = tgn.gID')
            ->where('tn.treeNodeID in (:nodeIDs)')
            ->setParameter('nodeIDs', $nodeIDs, Connection::PARAM_INT_ARRAY)
            ->execute()
        ;
        $frontier = [];
        while ($row = $result->fetchAssociative()) {
            $nodeID = (int) $row['treeNodeID'];
            $parentID = (int) $row['treeNodeParentID'];
            $this->treeNodesByID[$nodeID] = [
                'parentID' => $parentID,
                'groupName' => $row['gName'] === null ? null : (string) $row['gName'],
            ];
            if ($parentID > 0 && !isset($this->treeNodesByID[$parentID])) {
                $frontier[] = $parentID;
            }
        }

        return $frontier;
    }

    /**
     * @return int[]
     */
    private function normalizeIDs(array $ids): array
    {
        $normalized = [];
        foreach ($ids as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $normalized[$id] = $id;
            }
        }

        return array_values($normalized);
    }
}