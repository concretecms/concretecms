<?php

namespace Concrete\Tests\User;

use Concrete\Core\Entity;
use Concrete\Core\Tree\Node\NodeType as TreeNodeType;
use Concrete\Core\Tree\TreeType;
use Concrete\Core\Tree\Type\Group as GroupTreeType;
use Concrete\Core\User\Group\Command;
use Concrete\Core\User\Group\FolderManager;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\UserList;
use Concrete\TestHelpers\User\UserTestCase;

class GroupTest extends UserTestCase
{
    public function setUp(): void
    {
        $this->truncateTables();
        parent::setUp();

        TreeNodeType::add('group');
        TreeType::add('group');
        GroupTreeType::add();
        (new FolderManager())->create();
        Group::add(
            tc('GroupName', 'Guest'),
            tc('GroupDescription', 'The guest group represents unregistered visitors to your site.'),
            false,
            false,
            GUEST_GROUP_ID
        );
        Group::add(
            tc('GroupName', 'Registered Users'),
            tc('GroupDescription', 'The registered users group represents all user accounts.'),
            false,
            false,
            REGISTERED_GROUP_ID
        );
        Group::add(
            tc('GroupName', 'Administrators'),
            '',
            false,
            false,
            ADMIN_GROUP_ID
        );
    }

    public function testAutomatedGroupsBase()
    {
        require_once DIR_TESTS . '/assets/User/TestGroup.php';
        $g = Group::add('Test Group', ''); // gonna pull all users with vowels in their names in this group.
        $g->setAutomationOptions(true, false, false);

        $groupControllers = \Group::getAutomatedOnRegisterGroupControllers();
        $this->assertEquals(1, count($groupControllers));

        $users = [
            ['aembler', 'andrew@concrete5.org'],
            ['ffjdhbn', 'testuser1@concrete5.org'],
            ['ffbOkj', 'testuser2@concrete5.org'],
            ['kkytnz', 'testuser3@concrete5.org'],
            ['zzvnv', 'testuser4@concrete5.org'],
            ['qqwenz', 'testuser5@concrete5.org'],
            ['mmnvb', 'testuser6@concrete5.org'],
        ];
        foreach ($users as $user) {
            $this->createUser($user[0], $user[1]);
        }

        $ul = new UserList();
        $ul->filterByGroupID($g->getGroupID());
        $ul->sortByUserName();
        $users1 = $ul->getResults();

        $ul = new UserList();
        $ul->filterByNoGroup();
        $ul->sortByUserName();
        $users2 = $ul->getResults();

        $this->assertEquals(3, count($users1));
        $this->assertEquals(4, count($users2));
    }

    public function testUpdateGroup()
    {
        $originalGroup = Group::add('Old Group Name', 'This is the description');
        $newGroup = $originalGroup->update('New Group Name', 'This is the new description');

        $this->assertEquals('New Group Name', $newGroup->getGroupName());
        $this->assertEquals('This is the new description', $newGroup->getGroupDescription());
    }

    public function testRescanGroupPath()
    {
        $originalGroup = Group::add('Old Group for Rescan', 'This is a test group');
        $newGroup = $originalGroup->update('New Group for Rescan', 'This is a test group');
        $newPath = $newGroup->getGroupPath();
        $this->assertEquals('/New Group for Rescan', $newPath);
    }

    public function testHierarchicalGroup()
    {
        $group1 = Group::add('HGroup', 'This is a test group 1');
        $group2 = Group::add('Group 1', 'This is a test group 2', $group1);
        $group3 = Group::add('Approvers', 'This is a test group 1', $group2);
        $group4 = Group::add('Group 10', 'This is a test group 10', $group1);

        $parentOfGroup2 = $group2->getParentGroup();
        $this->assertNotNull($parentOfGroup2);
        $this->assertSame($group1->getGroupID(), $parentOfGroup2->getGroupID());
        $this->assertNull($group1->getParentGroup());

        $newPath = $group3->getGroupPath();
        $this->assertEquals('/HGroup/Group 1/Approvers', $newPath);

        $fui1 = $this->createUser('foobar1', 'foobar1@concrete5.org');
        $fui2 = $this->createUser('foobar2', 'foobar2@concrete5.org');
        $fui3 = $this->createUser('foobar3', 'foobar3@concrete5.org');
        $fui4 = $this->createUser('foobar4', 'foobar4@concrete5.org');
        $fuu1 = $fui1->getUserObject();
        $fuu1->enterGroup($group3);
        $fuu2 = $fui2->getUserObject();
        $fuu2->enterGroup($group3);
        $fuu3 = $fui3->getUserObject();
        $fuu3->enterGroup($group3);
        $fuu4 = $fui4->getUserObject();
        $fuu4->enterGroup($group2);

        $this->assertEquals(3, $group3->getGroupMembersNum());

        $ids = $group3->getGroupMemberIds();
        $this->assertEquals(1, $ids[0]);
        $this->assertEquals(2, $ids[1]);
        $this->assertEquals(3, $ids[2]);

        $users = $group3->getGroupMembers();
        $this->assertEquals('foobar2', $users[1]->getUserName());

        // Now we do the hierarchy
        $this->assertEquals(4, $group2->getGroupMembersNum());
        $ids = $group2->getGroupMemberIds();
        $this->assertEquals(1, $ids[0]);
        $this->assertEquals(2, $ids[1]);
        $this->assertEquals(3, $ids[2]);
        $this->assertEquals(4, $ids[3]);

        $users = $group2->getGroupMembers();
        $this->assertEquals('foobar3', $users[2]->getUserName());

        $this->assertEquals(4, $group1->getGroupMembersNum());
        $ids = $group2->getGroupMemberIds();
        $this->assertEquals(1, $ids[0]);
        $this->assertEquals(2, $ids[1]);
        $this->assertEquals(3, $ids[2]);
        $this->assertEquals(4, $ids[3]);

        $users = $group2->getGroupMembers();
        $userA = $users[2];
        $userB = $users[3];
        $this->assertEquals('foobar3', $userA->getUserName());
        $this->assertEquals('foobar4', $userB->getUserName());

        $userA = $userA->getUserObject();
        $this->assertTrue($userA->inGroup($group3));
        $this->assertTrue($userA->inGroup($group2));
        $this->assertTrue($userA->inGroup($group1));
        $this->assertFalse($userA->inGroup($group4));
        $userB = $userB->getUserObject();

        $this->assertFalse($userB->inGroup($group3));
        $this->assertTrue($userB->inGroup($group2));
        $this->assertTrue($userB->inGroup($group1));
        $this->assertFalse($userB->inGroup($group4));
    }

    public function testInMultipleGroups()
    {
        $groupA = Group::add('Group A', 'This is a test group A');
        $groupB = Group::add('Group B', 'This is a test group B');
        $groupC = Group::add('Group C', 'This is a test group C');
        $groupD = Group::add('Group D', 'This is a test group D - Child of C', $groupC);

        $users = [
            ['zxcvz1231', 'andrew@concrete5.org'],
            ['asdfzsdf', 'testuser1@concrete5.org'],
            ['sdfbzxc', 'testuser2@concrete5.org'],
            ['zksjdf7', 'testuser3@concrete5.org'],
            ['kdufiz', 'testuser4@concrete5.org'],
            ['swrefxzd', 'testuser5@concrete5.org'],
            ['abbabdf', 'testuser6@concrete5.org'],
            ['ffdsazs', 'testuser7@concrete5.org'],
            ['zxsdfer', 'testuser8@concrete5.org'],
        ];
        $i = 0;
        foreach ($users as $user) {
            $ui = $this->createUser($user[0], $user[1]);
            $user = $ui->getUserObject();
            if (in_array($i, [0, 2, 3])) {
                $user->enterGroup($groupA);
            } else {
                $user->enterGroup($groupB);
            }
            $i++;
        }

        $user = \UserInfo::getByName('zxsdfer');
        $user = $user->getUserObject();
        $user->enterGroup($groupA);

        $list1 = new UserList();
        $list1->filterByGroup($groupA);
        $results = $list1->getResults();
        $this->assertEquals(4, count($results));

        $list = new UserList();
        $list->filterByGroup($groupB);
        $this->assertEquals(-1, $list->getTotalResults());

        $list->filterByGroup($groupA);
        $results = $list->getResults();
        $this->assertEquals(1, count($results));

        $list2 = new UserList();
        $list2->ignorePermissions();
        $list2->filterByGroup($groupC);
        $this->assertEquals(0, $list2->getTotalResults());

        $list3 = new UserList();
        $list3->filterByGroup($groupB, false);
        $results = $list3->getResults();
        $this->assertEquals(3, count($results));

        // Check for child groups as well
        $user = \UserInfo::getByName('zxsdfer');
        $user = $user->getUserObject();
        $user->enterGroup($groupD);

        $user = \UserInfo::getByName('zxcvz1231');
        $user = $user->getUserObject();
        $user->enterGroup($groupC);
        $user->exitGroup($groupA);

        $list4 = new UserList();
        $list4->filterByInAnyGroup([$groupC], false);
        $results = $list4->getResults();
        $this->assertEquals(7, count($results));

        $list5 = new UserList();
        $list5->filterByInAnyGroup([$groupC, $groupA], false);
        $results = $list5->getResults();
        $this->assertEquals(5, count($results));

        $list6 = new UserList();
        $list6->filterByInAnyGroup([$groupB, $groupA], true);
        $results = $list6->getResults();
        $this->assertEquals(8, count($results));
    }

    public function testParentChildGroups()
    {
        $group1 = Group::add('User Group', 'This is a parent test group 1');
        $group2 = Group::add('User Group Child', 'This is a child test group 1', $group1);
        $group3 = Group::add('User Group Child 2', 'This is a child test group 2', $group1);

        $fui1 = $this->createUser('groupuser1', 'groupuser1@concrete5.org');
        $fui2 = $this->createUser('groupuser2', 'groupuser2@concrete5.org');
        $fui3 = $this->createUser('groupuser3', 'groupuser3@concrete5.org');
        $fui4 = $this->createUser('groupuser4', 'groupuser4@concrete5.org');
        $fuu1 = $fui1->getUserObject();
        $fuu1->enterGroup($group3);
        $fuu2 = $fui2->getUserObject();
        $fuu2->enterGroup($group1);
        $fuu3 = $fui3->getUserObject();
        $fuu3->enterGroup($group2);
        $fuu4 = $fui4->getUserObject();
        $fuu4->enterGroup($group3);
        $fuu4->enterGroup($group1);

        //Check they aren exclusively in group 1
        $this->assertFalse($fuu1->inExactGroup($group1));
        // Check they are a member of group 1
        $this->assertTrue($fuu1->inGroup($group1));
        // Check they are not a member of similar group path
        $this->assertFalse($fuu1->inGroup($group2));

        $this->assertTrue($fuu2->inExactGroup($group1));
        // Check they are a member of group 1
        $this->assertTrue($fuu2->inGroup($group1));
        // Check they are not a member of any children
        $this->assertFalse($fuu2->inGroup($group2));
        $this->assertFalse($fuu3->inGroup($group3));

        // Check they are a member of group 1 from child only
        $this->assertFalse($fuu3->inExactGroup($group1));
        $this->assertTrue($fuu3->inGroup($group1));
        // Check they are not a member of any children
        $this->assertTrue($fuu3->inGroup($group2));
        $this->assertFalse($fuu3->inGroup($group3));

        // Check they are a member of group 1
        $this->assertTrue($fuu4->inExactGroup($group1));
        $this->assertTrue($fuu4->inGroup($group1));
        // Check they are not a member of any children
        $this->assertFalse($fuu4->inGroup($group2));
        $this->assertTrue($fuu4->inGroup($group3));
    }

    public function testGroupRemoval(): void
    {
        /*
         * Create the following group tree:
         *
         * root
         *  ├── a     (without users)
         *  │
         *  ├── b     (without users)
         *  │
         *  ├─┬ c     (without users)
         *  │ ├── c1  (without users)
         *  │ └── c2  (without users)
         *  │
         *  ├─┬ d     (without users)
         *  │ ├── d1  (without users)
         *  │ └── d2  (without users)
         *  │
         *  ├─┬ e     (with users)
         *  │ └── e1  (without users)
         *  │
         *  ├─┬ f     (without users)
         *  │ ├── f1  (without users)
         *  │ ├─┬ f2  (with users)
         *  │ │ └ f21 (without users)
         *  │ └─┬ f3  (without users)
         *  │   └ f31 (without users)
         *  │
         *  ├─┬ g     (without users)
         *  │ └─┬ g1  (without users)
         *  │   └ g11 (without users)
         *  │
         *  └─┬ h     (without users)
         *    └─┬ h1  (without users)
         *      └ h11 (without users)
         */
        $app = app();
        $a = Group::add('a', '', null);
        $b = Group::add('b', '', null);
        $c = Group::add('c', '', null);
        $c1 = Group::add('c1', '', $c);
        $c2 = Group::add('c2', '', $c);
        $d = Group::add('d', '', null);
        $d1 = Group::add('d1', '', $d);
        $d2 = Group::add('d2', '', $d);
        $e = Group::add('e', '', null);
        $e1 = Group::add('e1', '', $e);
        $f = Group::add('f', '', null);
        $f1 = Group::add('f1', '', $f);
        $f2 = Group::add('f2', '', $f);
        $f21 = Group::add('f21', '', $f2);
        $f3 = Group::add('f3', '', $f);
        $f31 = Group::add('f31', '', $f3);
        $g = Group::add('g', '', null);
        $g1 = Group::add('g1', '', $g);
        $g11 = Group::add('g11', '', $g1);
        $h = Group::add('h', '', null);
        $h1 = Group::add('h1', '', $h);
        $h11 = Group::add('h11', '', $h1);

        $userInfo = $this->createUser('username', 'user@email.org');
        $user = $userInfo->getUserObject();
        foreach ([$e, $f2] as $group) {
            $user->enterGroup($group);
        }

        // Let's delete the group /a and check the "non-extended" result (backward compatible)
        $command = new Command\DeleteGroupCommand($a->getGroupID());
        $result = $app->executeCommand($command);
        $this->assertSame(null, $result);

        // Let's try again to delete the group /a
        $result = $app->executeCommand($command);
        $this->assertSame(false, $result);

        // Let's delete the group /b and check the new "extended" result
        $command = (new Command\DeleteGroupCommand($b->getGroupID()))
            ->setExtendedResults(true)
        ;
        $result = $app->executeCommand($command);
        $this->assertInstanceOf(Command\DeleteGroupCommand\Result::class, $result);
        /** @var \Concrete\Core\User\Group\Command\DeleteGroupCommand\Result $result */
        $this->assertSame(true, $result->isGroupDeleted($b->getGroupID()));
        $this->assertSame([$b->getGroupID()], $result->getDeletedGroupIDs());
        $this->assertSame(1, $result->getNumberOfDeletedGroups());
        $this->assertSame(0, $result->getNumberOfUndeletableGroups());
        $this->assertSame([], $result->getUndeletableGroups());

        // Let's try again to delete the group /b
        $result = $app->executeCommand($command);
        $this->assertInstanceOf(Command\DeleteGroupCommand\Result::class, $result);
        /** @var \Concrete\Core\User\Group\Command\DeleteGroupCommand\Result $result */
        $this->assertSame(false, $result->isGroupDeleted($b->getGroupID()));
        $this->assertSame([], $result->getDeletedGroupIDs());
        $this->assertSame(0, $result->getNumberOfDeletedGroups());
        $this->assertSame(1, $result->getNumberOfUndeletableGroups());
        $this->assertSame([$b->getGroupID()], array_keys($result->getUndeletableGroups()));

        // Let's check the "move to root" (backward compatible) option when deleting the group /c
        $this->assertSame($c1->getGroupID(), Group::getByPath('/c/c1')->getGroupID());
        $this->assertSame($c2->getGroupID(), Group::getByPath('/c/c2')->getGroupID());
        $command = (new Command\DeleteGroupCommand($c->getGroupID()))
            ->setExtendedResults(true)
        ;
        $result = $app->executeCommand($command);
        $this->assertInstanceOf(Command\DeleteGroupCommand\Result::class, $result);
        /** @var \Concrete\Core\User\Group\Command\DeleteGroupCommand\Result $result */
        $this->assertSame(true, $result->isGroupDeleted($c->getGroupID()));
        $this->assertSame([$c->getGroupID()], $result->getDeletedGroupIDs());
        $this->assertSame(1, $result->getNumberOfDeletedGroups());
        $this->assertSame(0, $result->getNumberOfUndeletableGroups());
        $this->assertSame([], $result->getUndeletableGroups());
        $this->assertNull(Group::getByPath('/c/c1'));
        $this->assertNull(Group::getByPath('/c/c2'));
        $this->assertSame($c1->getGroupID(), Group::getByPath('/c1')->getGroupID());
        $this->assertSame($c2->getGroupID(), Group::getByPath('/c2')->getGroupID());

        // Let's check the "abort" option when deleting the group /d (using the old "non extended" result)
        $command = (new Command\DeleteGroupCommand($d->getGroupID()))
            ->setOnChildGroups(Command\DeleteGroupCommand::ONCHILDGROUPS_ABORT)
        ;
        $result = $app->executeCommand($command);
        $this->assertSame(false, $result);

        // Let's check the "abort" option when deleting the group /d (using the new "extended" result)
        $command = (new Command\DeleteGroupCommand($d->getGroupID()))
            ->setExtendedResults(true)
            ->setOnChildGroups(Command\DeleteGroupCommand::ONCHILDGROUPS_ABORT)
        ;
        $result = $app->executeCommand($command);
        $this->assertInstanceOf(Command\DeleteGroupCommand\Result::class, $result);
        /** @var \Concrete\Core\User\Group\Command\DeleteGroupCommand\Result $result */
        $this->assertSame(false, $result->isGroupDeleted($d->getGroupID()));
        $this->assertSame(false, $result->isGroupDeleted($d1->getGroupID()));
        $this->assertSame(false, $result->isGroupDeleted($d2->getGroupID()));
        $this->assertSame([], $result->getDeletedGroupIDs());
        $this->assertSame(0, $result->getNumberOfDeletedGroups());
        $this->assertSame(1, $result->getNumberOfUndeletableGroups());
        $this->assertSame([$d->getGroupID()], array_keys($result->getUndeletableGroups()));
        $this->assertSame($d->getGroupID(), Group::getByPath('/d')->getGroupID());
        $this->assertSame($d1->getGroupID(), Group::getByPath('/d/d1')->getGroupID());
        $this->assertSame($d2->getGroupID(), Group::getByPath('/d/d2')->getGroupID());

        // Let's check the "cascade delete" option when deleting the group /d (using the new "extended" result)
        $command = (new Command\DeleteGroupCommand($d->getGroupID()))
            ->setExtendedResults(true)
            ->setOnChildGroups(Command\DeleteGroupCommand::ONCHILDGROUPS_DELETE)
        ;
        $result = $app->executeCommand($command);
        $this->assertInstanceOf(Command\DeleteGroupCommand\Result::class, $result);
        /** @var \Concrete\Core\User\Group\Command\DeleteGroupCommand\Result $result */
        $this->assertSame(true, $result->isGroupDeleted($d->getGroupID()));
        $this->assertSame(true, $result->isGroupDeleted($d1->getGroupID()));
        $this->assertSame(true, $result->isGroupDeleted($d2->getGroupID()));
        $this->assertEqualsCanonicalizing([$d->getGroupID(), $d1->getGroupID(), $d2->getGroupID()], $result->getDeletedGroupIDs());
        $this->assertSame(3, $result->getNumberOfDeletedGroups());
        $this->assertSame(0, $result->getNumberOfUndeletableGroups());
        $this->assertSame([], $result->getUndeletableGroups());
        $this->assertNull(Group::getByID($d->getGroupID()));
        $this->assertNull(Group::getByID($d1->getGroupID()));
        $this->assertNull(Group::getByID($d2->getGroupID()));

        // Test skipping deleting a group with members
        $command = (new Command\DeleteGroupCommand($e->getGroupID()))
            ->setExtendedResults(true)
            ->setOnlyIfEmpty(true)
        ;
        $result = $app->executeCommand($command);
        $this->assertInstanceOf(Command\DeleteGroupCommand\Result::class, $result);
        /** @var \Concrete\Core\User\Group\Command\DeleteGroupCommand\Result $result */
        $this->assertSame(false, $result->isGroupDeleted($e->getGroupID()));
        $this->assertSame([], $result->getDeletedGroupIDs());
        $this->assertSame(0, $result->getNumberOfDeletedGroups());
        $this->assertSame(1, $result->getNumberOfUndeletableGroups());
        $this->assertSame([$e->getGroupID()], array_keys($result->getUndeletableGroups()));
        $this->assertSame($e->getGroupID(), Group::getByPath('/e')->getGroupID());
        $this->assertSame($e1->getGroupID(), Group::getByPath('/e/e1')->getGroupID());

        // Test deleting a group with members (backward compatible)
        $command = (new Command\DeleteGroupCommand($e->getGroupID()))
            ->setExtendedResults(true)
        ;
        $result = $app->executeCommand($command);
        $this->assertInstanceOf(Command\DeleteGroupCommand\Result::class, $result);
        /** @var \Concrete\Core\User\Group\Command\DeleteGroupCommand\Result $result */
        $this->assertSame(true, $result->isGroupDeleted($e->getGroupID()));
        $this->assertSame([$e->getGroupID()], $result->getDeletedGroupIDs());
        $this->assertSame(1, $result->getNumberOfDeletedGroups());
        $this->assertSame(0, $result->getNumberOfUndeletableGroups());
        $this->assertSame([], $result->getUndeletableGroups());
        $this->assertNull(Group::getByID($e->getGroupID()));

        // Test cascade deleting groups when a child group can't be deleted
        $command = (new Command\DeleteGroupCommand($f->getGroupID()))
            ->setExtendedResults(true)
            ->setOnlyIfEmpty(true)
            ->setOnChildGroups(Command\DeleteGroupCommand::ONCHILDGROUPS_DELETE)
        ;
        $result = $app->executeCommand($command);
        $this->assertInstanceOf(Command\DeleteGroupCommand\Result::class, $result);
        /** @var \Concrete\Core\User\Group\Command\DeleteGroupCommand\Result $result */
        $this->assertSame(false, $result->isGroupDeleted($f->getGroupID()));
        $this->assertSame(true, $result->isGroupDeleted($f1->getGroupID()));
        $this->assertSame(false, $result->isGroupDeleted($f2->getGroupID()));
        $this->assertSame(true, $result->isGroupDeleted($f3->getGroupID()));
        $this->assertEqualsCanonicalizing([$f1->getGroupID(), $f3->getGroupID(), $f31->getGroupID()], $result->getDeletedGroupIDs());
        $this->assertSame(3, $result->getNumberOfDeletedGroups());
        $this->assertSame(2, $result->getNumberOfUndeletableGroups());
        $this->assertEqualsCanonicalizing([$f->getGroupID(), $f2->getGroupID()], array_keys($result->getUndeletableGroups()));
        $this->assertSame($f->getGroupID(), Group::getByPath('/f')->getGroupID());
        $this->assertSame($f2->getGroupID(), Group::getByPath('/f/f2')->getGroupID());
        $this->assertSame($f21->getGroupID(), Group::getByPath('/f/f2/f21')->getGroupID());

        // Test moving to parent group (when there group being deleted is a sub-group)
        $command = (new Command\DeleteGroupCommand($g1->getGroupID()))
            ->setExtendedResults(true)
            ->setOnChildGroups(Command\DeleteGroupCommand::ONCHILDGROUPS_MOVETOPARENT)
        ;
        $result = $app->executeCommand($command);
        $this->assertInstanceOf(Command\DeleteGroupCommand\Result::class, $result);
        /** @var \Concrete\Core\User\Group\Command\DeleteGroupCommand\Result $result */
        $this->assertSame(false, $result->isGroupDeleted($g->getGroupID()));
        $this->assertSame(true, $result->isGroupDeleted($g1->getGroupID()));
        $this->assertSame(false, $result->isGroupDeleted($g11->getGroupID()));
        $this->assertSame([$g1->getGroupID()], $result->getDeletedGroupIDs());
        $this->assertSame(1, $result->getNumberOfDeletedGroups());
        $this->assertSame(0, $result->getNumberOfUndeletableGroups());
        $this->assertSame([], array_keys($result->getUndeletableGroups()));
        $this->assertSame($g->getGroupID(), Group::getByPath('/g')->getGroupID());
        $this->assertSame($g11->getGroupID(), Group::getByPath('/g/g11')->getGroupID());

        // Test moving to parent group (when there group being deleted is not a sub-group)
        $command = (new Command\DeleteGroupCommand($h->getGroupID()))
            ->setExtendedResults(true)
            ->setOnChildGroups(Command\DeleteGroupCommand::ONCHILDGROUPS_MOVETOPARENT)
        ;
        $result = $app->executeCommand($command);
        $this->assertInstanceOf(Command\DeleteGroupCommand\Result::class, $result);
        /** @var \Concrete\Core\User\Group\Command\DeleteGroupCommand\Result $result */
        $this->assertSame(true, $result->isGroupDeleted($h->getGroupID()));
        $this->assertSame(false, $result->isGroupDeleted($h1->getGroupID()));
        $this->assertSame(false, $result->isGroupDeleted($h11->getGroupID()));
        $this->assertSame([$h->getGroupID()], $result->getDeletedGroupIDs());
        $this->assertSame(1, $result->getNumberOfDeletedGroups());
        $this->assertSame(0, $result->getNumberOfUndeletableGroups());
        $this->assertSame([], array_keys($result->getUndeletableGroups()));
        $this->assertSame($h1->getGroupID(), Group::getByPath('/h1')->getGroupID());
        $this->assertSame($h11->getGroupID(), Group::getByPath('/h1/h11')->getGroupID());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getMetadatas()
     */
    protected function getMetadatas()
    {
        $this->metadatas = array_values(array_unique(array_merge($this->metadatas, [
            Entity\User\GroupCreate::class,
            Entity\User\GroupSignup::class,
            Entity\User\GroupSignupRequest::class,
        ])));

        return parent::getMetadatas();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\TestHelpers\Database\ConcreteDatabaseTestCase::getTables()
     */
    protected function getTables()
    {
        $this->tables = array_values(array_unique(array_merge($this->tables, [
            'GroupSelectedRoles',
            'TreeGroupFolderNodes',
            'TreeGroupFolderNodeSelectedGroupTypes',
        ])));

        return parent::getTables();
    }
}
