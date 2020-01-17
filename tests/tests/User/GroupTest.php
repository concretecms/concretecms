<?php

namespace Concrete\Tests\User;

use Concrete\Core\Tree\Node\NodeType as TreeNodeType;
use Concrete\Core\Tree\TreeType;
use Concrete\Core\Tree\Type\Group as GroupTreeType;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\UserList;
use Concrete\TestHelpers\User\UserTestCase;

class GroupTest extends UserTestCase
{
    protected function setUp()
    {
        $this->truncateTables();
        parent::setUp();

        TreeNodeType::add('group');
        TreeType::add('group');
        GroupTreeType::add();
        $g1 = Group::add(
            tc('GroupName', 'Guest'),
            tc('GroupDescription', 'The guest group represents unregistered visitors to your site.'),
            false,
            false,
            GUEST_GROUP_ID
        );
        $g2 = Group::add(
            tc('GroupName', 'Registered Users'),
            tc('GroupDescription', 'The registered users group represents all user accounts.'),
            false,
            false,
            REGISTERED_GROUP_ID
        );
        $g3 = Group::add(tc('GroupName', 'Administrators'), '', false, false, ADMIN_GROUP_ID);
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
            ++$i;
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
}
