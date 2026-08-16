<?php

declare(strict_types=1);

namespace Concrete\Tests\User;

use Concrete\Core\Tree\Node\NodeType as TreeNodeType;
use Concrete\Core\Tree\TreeType;
use Concrete\Core\Tree\Type\Group as GroupTreeType;
use Concrete\Core\User\Group\FolderManager;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\MembershipExpirationChecker;
use Concrete\Core\User\Search\ColumnSet\Available;
use Concrete\Core\User\Search\ColumnSet\Column\DirectMembershipsColumn;
use Concrete\Core\User\Search\ColumnSet\Column\DirectMembershipsCountColumn;
use Concrete\Core\User\Search\ColumnSet\Column\IndirectMembershipsColumn;
use Concrete\Core\User\Search\ColumnSet\DefaultSet;
use Concrete\Core\User\Search\MembershipsProvider;
use Concrete\TestHelpers\User\UserTestCase;
use Doctrine\DBAL\Logging\DebugStack;

defined('C5_EXECUTE') or die('Access Denied.');

class SearchMembershipColumnsTest extends UserTestCase
{
    /**
     * @var MembershipsProvider
     */
    private $membershipsProvider;

    protected function getTables()
    {
        return array_merge(parent::getTables(), [
            'TreeGroupFolderNodes',
            'TreeGroupFolderNodeSelectedGroupTypes',
        ]);
    }

    public function setUp(): void
    {
        $this->truncateTables();
        parent::setUp();

        TreeNodeType::add('group');
        TreeType::add('group');
        GroupTreeType::add();
        (new FolderManager())->create();
        Group::add(tc('GroupName', 'Guest'), '');
        Group::add(tc('GroupName', 'Registered Users'), '');
        Group::add(tc('GroupName', 'Administrators'), '');

        $this->membershipsProvider = app(MembershipsProvider::class);
        $this->membershipsProvider->clear();
    }

    public function tearDown(): void
    {
        $this->membershipsProvider->clear();
        parent::tearDown();
    }

    public function testMembershipColumnsAreAvailableButNotDefault(): void
    {
        $columns = new Available();

        static::assertInstanceOf(
            DirectMembershipsCountColumn::class,
            $columns->getColumnByKey('directMembershipsCount')
        );
        static::assertInstanceOf(DirectMembershipsColumn::class, $columns->getColumnByKey('directMemberships'));
        static::assertInstanceOf(IndirectMembershipsColumn::class, $columns->getColumnByKey('indirectMemberships'));
        static::assertFalse($columns->getColumnByKey('directMembershipsCount')->isColumnSortable());
        static::assertFalse($columns->getColumnByKey('directMemberships')->isColumnSortable());
        static::assertFalse($columns->getColumnByKey('indirectMemberships')->isColumnSortable());

        $defaultColumns = new DefaultSet();
        static::assertNull($defaultColumns->getColumnByKey('directMembershipsCount'));
        static::assertNull($defaultColumns->getColumnByKey('directMemberships'));
        static::assertNull($defaultColumns->getColumnByKey('indirectMemberships'));
    }

    public function testMembershipValuesAndIndirectPathsAreSortedByDirectGroup(): void
    {
        $zetaParent = Group::add('Zeta Parent', '');
        $alphaChild = Group::add('Alpha Child', '', $zetaParent);
        $alphaParent = Group::add('Alpha Parent', '');
        $alphaMiddle = Group::add('Middle Group', '', $alphaParent);
        $zetaChild = Group::add('Zeta Child', '', $alphaMiddle);
        $standalone = Group::add('Beta Standalone', '');

        $userInfo = $this->createUser('membership-columns', 'membership-columns@example.org');
        $user = $userInfo->getUserObject();
        $user->enterGroup($zetaChild);
        $user->enterGroup($standalone);
        $user->enterGroup($alphaChild);

        $direct = new DirectMembershipsColumn();
        $directCount = new DirectMembershipsCountColumn();
        $indirect = new IndirectMembershipsColumn();

        static::assertSame(3, $directCount->getColumnValue($userInfo));
        static::assertSame(
            '<div class="d-flex flex-wrap gap-1 ccm-user-memberships-direct">'
            . '<span class="ccm-user-membership-chain">'
            . '<span class="badge bg-light text-dark border text-wrap text-start">Alpha Child</span></span>'
            . '<span class="ccm-user-membership-chain">'
            . '<span class="badge bg-light text-dark border text-wrap text-start">Beta Standalone</span></span>'
            . '<span class="ccm-user-membership-chain">'
            . '<span class="badge bg-light text-dark border text-wrap text-start">Zeta Child</span></span>'
            . '</div>',
            $direct->getColumnValue($userInfo)
        );
        static::assertSame(
            '<div class="d-flex flex-wrap gap-1 ccm-user-memberships-indirect">'
            . '<span class="ccm-user-membership-chain">'
            . '<span class="badge bg-light text-dark border text-wrap text-start">'
            . 'Zeta Parent &gt; Alpha Child</span></span>'
            . '<span class="ccm-user-membership-chain">'
            . '<span class="badge bg-light text-dark border text-wrap text-start">'
            . 'Alpha Parent &gt; Middle Group &gt; Zeta Child</span></span>'
            . '</div>',
            $indirect->getColumnValue($userInfo)
        );
    }

    public function testMembershipValuesAreEscaped(): void
    {
        $parent = Group::add('Parent <script>', '');
        $child = Group::add('Child <script>', '', $parent);
        $userInfo = $this->createUser('membership-escaping', 'membership-escaping@example.org');
        $userInfo->getUserObject()->enterGroup($child);

        $direct = new DirectMembershipsColumn();
        $indirect = new IndirectMembershipsColumn();

        static::assertSame(
            '<div class="d-flex flex-wrap gap-1 ccm-user-memberships-direct">'
            . '<span class="ccm-user-membership-chain">'
            . '<span class="badge bg-light text-dark border text-wrap text-start">'
            . 'Child &lt;script&gt;</span></span>'
            . '</div>',
            $direct->getColumnValue($userInfo)
        );
        static::assertSame(
            '<div class="d-flex flex-wrap gap-1 ccm-user-memberships-indirect">'
            . '<span class="ccm-user-membership-chain">'
            . '<span class="badge bg-light text-dark border text-wrap text-start">'
            . 'Parent &lt;script&gt; &gt; Child &lt;script&gt;</span></span>'
            . '</div>',
            $indirect->getColumnValue($userInfo)
        );
    }

    public function testGroupFoldersAreExcludedFromIndirectPaths(): void
    {
        $folderManager = new FolderManager();
        $folder = $folderManager->addFolder($folderManager->getRootFolder(), 'Organizational Folder');
        $parent = Group::addBeneathFolder('Visible Parent', '', $folder);
        $child = Group::add('Visible Child', '', $parent);
        $userInfo = $this->createUser('membership-folder', 'membership-folder@example.org');
        $userInfo->getUserObject()->enterGroup($child);

        $indirect = new IndirectMembershipsColumn();

        static::assertSame(
            '<div class="d-flex flex-wrap gap-1 ccm-user-memberships-indirect">'
            . '<span class="ccm-user-membership-chain">'
            . '<span class="badge bg-light text-dark border text-wrap text-start">'
            . 'Visible Parent &gt; Visible Child</span></span>'
            . '</div>',
            $indirect->getColumnValue($userInfo)
        );
    }

    public function testUsersWithoutCustomGroupsHaveEmptyMembershipColumns(): void
    {
        $userInfo = $this->createUser('membership-empty', 'membership-empty@example.org');

        static::assertSame(0, (new DirectMembershipsCountColumn())->getColumnValue($userInfo));
        static::assertSame('', (new DirectMembershipsColumn())->getColumnValue($userInfo));
        static::assertSame('', (new IndirectMembershipsColumn())->getColumnValue($userInfo));
    }

    public function testExpiredMembershipsAreExcluded(): void
    {
        $group = Group::add('Expired Group', '');
        $userInfo = $this->createUser('membership-expired', 'membership-expired@example.org');
        $userInfo->getUserObject()->enterGroup($group);
        $group->setGroupExpirationByDateTime('2000-01-01 00:00:00', 'REMOVE');

        static::assertSame(0, (new DirectMembershipsCountColumn())->getColumnValue($userInfo));
        static::assertSame('', (new DirectMembershipsColumn())->getColumnValue($userInfo));
        static::assertSame('', (new IndirectMembershipsColumn())->getColumnValue($userInfo));
    }

    public function testMembershipExpirationRules(): void
    {
        $checker = app(MembershipExpirationChecker::class);
        $entered = '2000-01-01 00:00:00 UTC';
        $enteredTimestamp = (int) strtotime($entered);

        static::assertFalse($checker->isExpired(false, 'SET_TIME', $entered, 0, null, $enteredTimestamp + 1));
        static::assertTrue($checker->isExpired(true, 'SET_TIME', $entered, 0, null, $enteredTimestamp + 1));
        static::assertFalse($checker->isExpired(
            true,
            'SET_TIME',
            '2100-01-01 00:00:00 UTC',
            0,
            null,
            $enteredTimestamp + 1
        ));
        static::assertFalse($checker->isExpired(true, 'INTERVAL', null, 60, $entered, $enteredTimestamp + 3599));
        static::assertTrue($checker->isExpired(true, 'INTERVAL', null, 60, $entered, $enteredTimestamp + 3601));
        static::assertFalse($checker->isExpired(true, 'UNKNOWN', null, 0, null, $enteredTimestamp + 1));
    }

    public function testDirectCountDoesNotQueryTheGroupTree(): void
    {
        $group = Group::add('Direct Only', '');
        $userInfo = $this->createUser('membership-direct-only', 'membership-direct-only@example.org');
        $userInfo->getUserObject()->enterGroup($group);

        $configuration = $this->connection()->getConfiguration();
        $originalLogger = $configuration->getSQLLogger();
        $logger = new DebugStack();
        $configuration->setSQLLogger($logger);
        try {
            $this->membershipsProvider->clear();
            $count = $this->membershipsProvider->getDirectMembershipsCount((int) $userInfo->getUserID());
        } finally {
            $configuration->setSQLLogger($originalLogger);
        }

        static::assertSame(1, $count);
        static::assertCount(1, $logger->queries);
        $query = reset($logger->queries);
        static::assertStringNotContainsString('TreeNodes', $query['sql']);
        static::assertStringNotContainsString('TreeGroupNodes', $query['sql']);
    }

    public function testBatchPreloadQueryCountDoesNotGrowWithTheNumberOfUsers(): void
    {
        $parent = Group::add('Batch Parent', '');
        $userIDs = [];
        for ($i = 1; $i <= 10; $i++) {
            $child = Group::add("Batch Child {$i}", '', $parent);
            $userInfo = $this->createUser("membership-batch-{$i}", "membership-batch-{$i}@example.org");
            $userInfo->getUserObject()->enterGroup($child);
            $userIDs[] = (int) $userInfo->getUserID();
        }

        $configuration = $this->connection()->getConfiguration();
        $originalLogger = $configuration->getSQLLogger();
        $logger = new DebugStack();
        $configuration->setSQLLogger($logger);
        try {
            $this->membershipsProvider->clear();
            $this->membershipsProvider->preload([$userIDs[0]], true);
            $singleUserQueryCount = count($logger->queries);

            $logger->queries = [];
            $this->membershipsProvider->clear();
            $this->membershipsProvider->preload($userIDs, true);
            $multipleUsersQueryCount = count($logger->queries);
        } finally {
            $configuration->setSQLLogger($originalLogger);
        }

        static::assertSame($singleUserQueryCount, $multipleUsersQueryCount);
        static::assertLessThanOrEqual(4, $multipleUsersQueryCount);
    }
}
