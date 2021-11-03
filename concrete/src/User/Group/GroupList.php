<?php /** @noinspection PhpUnused */

namespace Concrete\Core\User\Group;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Package;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\ItemList\Database\ItemList as DatabaseItemList;
use Concrete\Core\Search\ItemList\Pager\Manager\PagerManagerInterface;
use Concrete\Core\Search\ItemList\Pager\Manager\UserGroupPagerManager;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\ItemList\Pager\QueryString\VariableFactory;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\Pagination\PaginationProviderInterface;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\User\User;
use Pagerfanta\Adapter\DoctrineDbalAdapter;
use Closure;

class GroupList extends DatabaseItemList implements PagerProviderInterface, PaginationProviderInterface
{
    protected $enableAutomaticSorting = false;

    /**
     * @var Closure | integer | null
     */
    protected $permissionsChecker;

    protected $includeAllGroups = false;

    protected $autoSortColumns = ['g.gName', 'g.gID'];

    /** @var GroupRepository */
    protected $groupRepository;

    public function includeAllGroups()
    {
        $this->includeAllGroups = true;
    }

    /**
     * Filters keyword fields by keywords (including name and description).
     *
     * @param $keywords
     */
    public function filterByKeywords($keywords)
    {
        $expressions = [
            $this->query->expr()->like('g.gName', ':keywords'),
            $this->query->expr()->like('g.gDescription', ':keywords'),
        ];

        $expr = $this->query->expr();
        $this->query->andWhere(call_user_func_array([$expr, 'orX'], $expressions));
        $this->query->setParameter('keywords', '%' . $keywords . '%');
    }

    public function filterByPackage(Package $package)
    {
        $this->query->andWhere('pkgID = :pkgID');
        $this->query->setParameter('pkgID', $package->getPackageID());
    }

    public function filterByExpirable()
    {
        $this->query->andWhere('gUserExpirationIsEnabled', ':gUserExpirationIsEnabled');
        $this->query->setParameter('gUserExpirationIsEnabled', 1);
    }

    /**
     * Only return groups the user has the ability to assign.
     */
    public function filterByAssignable()
    {
        $app = Application::getFacadeApplication();
        /** @var Repository $config */
        $config = $app->make(Repository::class);

        if ($config->get('concrete.permissions.model') != 'simple') {
            // there's gotta be a more reasonable way than this but right now i'm not sure what that is.
            $excludeGroupIDs = [GUEST_GROUP_ID, REGISTERED_GROUP_ID];
            /** @var Connection $db */
            $db = $app->make(Connection::class);
            /** @noinspection PhpUnhandledExceptionInspection */
            /** @noinspection SqlNoDataSourceInspection */
            $r = $db->executeQuery('select gID from ' . $db->getDatabasePlatform()->quoteSingleIdentifier('Groups') . ' where gID > ?', [REGISTERED_GROUP_ID]);
            while ($row = $r->fetch()) {
                $g = $this->getGroupRepository()->getGroupById($row['gID']);
                $gp = new Checker($g);
                /** @noinspection PhpUndefinedMethodInspection */
                if (!$gp->canAssignGroup()) {
                    $excludeGroupIDs[] = $row['gID'];
                }
            }
            $this->query->andWhere(
                $this->query->expr()->notIn('g.gId', array_map([$db, 'quote'], $excludeGroupIDs))
            );
        }
    }

    /**
     * Only return groups the user is actually a member of
     */
    public function filterByHavingMembership()
    {
        $app = Facade::getFacadeApplication();
        /** @var User $u */
        $u = $app->make(User::class);
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        if ($u->isRegistered()) {
            $groups = $u->getUserGroups(); // returns an array of IDs
            $this->query->andWhere(
                $this->query->expr()->in('g.gId', array_map([$db, 'quote'], $groups))
            );
        }
    }

    public function filterByParentGroup(Group $parent)
    {
        $this->query->andWhere($this->query->expr()->like('g.gPath', ':gPath'));
        $this->query->setParameter('gPath', $parent->getGroupPath() . '/%');
    }

    public function filterByGroupType(GroupType $groupType)
    {
        $this->query->andWhere('g.gtID = :gtID');
        $this->query->setParameter('gtID', $groupType->getId());
    }

    public function filterByUserID($uID)
    {
        $this->query->innerJoin('g', 'UserGroups', 'ug', 'g.gID = ug.gID');
        $this->query->andWhere('ug.uID = :uID');
        $this->query->setParameter('uID', $uID);
    }

    public function filterByName($gName)
    {
        $this->query->andWhere('g.gName LIKE :gName');
        $this->query->setParameter('gName', "%" . $gName . "%");
    }

    public function createQuery()
    {
        $this->query->select('g.gID')
            ->from($this->query->getConnection()->getDatabasePlatform()->quoteSingleIdentifier('Groups'), 'g');
    }

    public function finalizeQuery(\Doctrine\DBAL\Query\QueryBuilder $query)
    {
        if (!$this->includeAllGroups) {
            $query->andWhere('g.gID > :minGroupID');
            $query->setParameter('minGroupID', REGISTERED_GROUP_ID);
        }

        return $query;
    }

    /**
     * The total results of the query.
     *
     * @return int
     */
    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();

        return $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct g.gID)')->setMaxResults(1)->execute()->fetchColumn();
    }

    /**
     * Gets the pagination object for the query.
     *
     * @return Pagination
     */
    protected function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct g.gID)')->setMaxResults(1);
        });
        $pagination = new Pagination($this, $adapter);

        return $pagination;
    }

    /**
     * @param $queryRow
     *
     * @return \Concrete\Core\User\Group\Group
     */
    public function getResult($queryRow)
    {
        $g = Group::getByID($queryRow['gID']);

        return $g;
    }

    /**
     * @return PagerManagerInterface
     */
    function getPagerManager()
    {
        return new UserGroupPagerManager($this);
    }

    /**
     * @return VariableFactory
     */
    function getPagerVariableFactory()
    {
        return new VariableFactory($this, $this->getSearchRequest());
    }

    /**
     * Returns the standard pagination adapter. This is used for
     * non-permissioned objects and is typically something like
     * DoctrineDbalAdapter
     * @return mixed
     */
    function getPaginationAdapter()
    {
        return new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            // We need to reset the potential custom order by here because otherwise, if we've added
            // items to the select parts, and we're ordering by them, we get a SQL error
            // when we get total results, because we're resetting the select
            $query->resetQueryParts(['groupBy', 'orderBy'])->select('count(distinct g.gID)')->setMaxResults(1);
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

        $cp = new Checker($mixed);

        /** @noinspection PhpUndefinedMethodInspection */
        return $cp->canViewUser();
    }

    public function setPermissionsChecker(?\Closure $checker = null)
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

    /**
     * @param GroupRepository $value
     *
     * @return $this;
     */
    public function setGroupRepository(GroupRepository $value)
    {
        $this->groupRepository = $value;

        return $this;
    }

    /**
     * @return GroupRepository
     */
    public function getGroupRepository()
    {
        if ($this->groupRepository === null) {
            $this->groupRepository = Application::getFacadeApplication()->make(GroupRepository::class);
        }

        return $this->groupRepository;
    }
}
