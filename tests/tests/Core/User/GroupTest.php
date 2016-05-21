<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 6/28/14
 * Time: 10:30 AM.
 */
namespace Concrete\Tests\Core\User;

use Concrete\Core\User\Group\Group;
use Concrete\Core\User\UserList;
use Concrete\Core\Tree\Type\Group as GroupTreeType;
use Concrete\Core\Tree\TreeType;
use Concrete\Core\Tree\Node\NodeType as TreeNodeType;

class GroupTest extends \UserTestCase
{
    protected $metadatas = array(
        'Concrete\Core\Entity\Attribute\Category',
        'Concrete\Core\Entity\Attribute\Key\Key',
        'Concrete\Core\Entity\Attribute\Key\UserKey',
    );

    protected function setUp()
    {
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
}
