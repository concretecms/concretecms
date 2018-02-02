<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 6/28/14
 * Time: 10:30 AM.
 */
namespace Concrete\Tests\Core\User;

use Concrete\Core\User\Group\Group;
use Concrete\Core\User\User;
use Concrete\Core\User\UserList;
use Concrete\Core\Tree\Type\Group as GroupTreeType;
use Concrete\Core\Tree\TreeType;
use Concrete\Core\Tree\Node\NodeType as TreeNodeType;

class GroupTest extends \UserTestCase
{
    protected function setUp()
    {
        $this->truncateTables();
        parent::setUp();

        TreeNodeType::add('group');
        TreeType::add('group');
        GroupTreeType::add();
        $g1 = Group::add(
            tc("GroupName", "Guest"),
            tc("GroupDescription", "The guest group represents unregistered visitors to your site."),
            false,
            false,
            GUEST_GROUP_ID
        );
        $g2 = Group::add(
            tc("GroupName", "Registered Users"),
            tc("GroupDescription", "The registered users group represents all user accounts."),
            false,
            false,
            REGISTERED_GROUP_ID
        );
        $g3 = Group::add(tc("GroupName", "Administrators"), "", false, false, ADMIN_GROUP_ID);
    }

    public function testAutomatedGroupsBase()
    {
        require_once dirname(__FILE__) . '/fixtures/TestGroup.php';
        $g = Group::add('Test Group', ''); // gonna pull all users with vowels in their names in this group.
        $g->setAutomationOptions(true, false, false);

        $groupControllers = \Group::getAutomatedOnRegisterGroupControllers();
        $this->assertEquals(1, count($groupControllers));

        $users = array(
            array('aembler', 'andrew@concrete5.org'),
            array('ffjdhbn', 'testuser1@concrete5.org'),
            array('ffbOkj', 'testuser2@concrete5.org'),
            array('kkytnz', 'testuser3@concrete5.org'),
            array('zzvnv', 'testuser4@concrete5.org'),
            array('qqwenz', 'testuser5@concrete5.org'),
            array('mmnvb', 'testuser6@concrete5.org'),
        );
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
        $userB = $userB->getUserObject();

        $this->assertFalse($userB->inGroup($group3));
        $this->assertTrue($userB->inGroup($group2));
        $this->assertTrue($userB->inGroup($group1));
    }

    public function testInMultipleGroups()
    {
        $groupA = Group::add('Group A', 'This is a test group A');
        $groupB = Group::add('Group B', 'This is a test group B');
        $groupC = Group::add('Group C', 'This is a test group C');

        $users = array(
            array('zxcvz1231', 'andrew@concrete5.org'),
            array('asdfzsdf', 'testuser1@concrete5.org'),
            array('sdfbzxc', 'testuser2@concrete5.org'),
            array('zksjdf7', 'testuser3@concrete5.org'),
            array('kdufiz', 'testuser4@concrete5.org'),
            array('swrefxzd', 'testuser5@concrete5.org'),
            array('abbabdf', 'testuser6@concrete5.org'),
            array('ffdsazs', 'testuser7@concrete5.org'),
            array('zxsdfer', 'testuser8@concrete5.org'),
        );
        $i = 0;
        foreach ($users as $user) {
            $ui = $this->createUser($user[0], $user[1]);
            $user = $ui->getUserObject();
            if (in_array($i, [0,2,3])) {
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

    }
}
