<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 6/28/14
 * Time: 10:30 AM.
 */
namespace Concrete\Tests\Core\User;

use Concrete\Core\User\Group\Group;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserList;

class UserListTest extends \UserTestCase
{
    /**
     * @var \Concrete\Core\User\UserList
     */
    protected $list;

    protected $userData = array(
        array(
            'testuser', 'testuser@concrete5.org',
        ),
        array(
            'testuser2', 'testuser2@concrete5.org',
        ),
        array(
            'andrew', 'andrew@concrete5.org',
        ),

    );

    public function setUp()
    {
        parent::setUp();

        foreach ($this->userData as $data) {
            $ui = call_user_func_array(array($this, 'createUser'), $data);
            $ui->reindex();
        }

        $this->list = new \Concrete\Core\User\UserList();
    }

    public function testTotal()
    {
        $total = $this->list->getTotalResults();
        $this->assertEquals(3, $total);
    }

    public function testUsername()
    {
        $this->list->filterByUserName('andrew');
        $pagination = $this->list->getPagination();
        $results = $pagination->getTotalResults();
        $this->assertEquals(1, $results);
        $this->assertEquals(1, $pagination->getNBResults());
        $results = $pagination->getCurrentPageResults();
        $this->assertInstanceOf('\Concrete\Core\User\UserInfo', $results[0]);
    }

    public function testKeywords()
    {
        $this->list->filterByKeywords('testu');
        $this->assertEquals(2, $this->list->getTotalResults());
        $this->list->filterByKeywords('.org');
        $this->assertEquals(3, $this->list->getTotalResults());
    }

    public function testGroups()
    {
        $u = \User::getByUserID(2);
        $g = Group::add('Test Group', 'Test Group');
        $u->enterGroup($g);

        $this->list->filterByGroup($g);
        $this->assertEquals(1, $this->list->getTotalResults());
        $pagination = $this->list->getPagination();
        $this->assertEquals(1, $pagination->getTotalResults());
        $results = $pagination->getCurrentPageResults();
        $this->assertInstanceOf('\Concrete\Core\User\UserInfo', $results[0]);
        $this->assertEquals('testuser2', $results[0]->getUserName());

        $nl = new UserList();
        $nl->filterByGroup($g, false);
        $nl->sortByUserID();
        $this->assertEquals(2, $nl->getTotalResults());
        $results = $nl->getResults();
        $this->assertEquals('testuser', $results[0]->getUserName());
        $this->assertEquals('andrew', $results[1]->getUserName());
    }

    public function testUserIDs()
    {
        $this->list->sortByUserID();
        $this->assertEquals(3, $this->list->getTotalResults());
        $results = $this->list->getResultIDs();
        $this->assertEquals(3, count($results));
        $this->assertEquals(1, $results[0]);
        $this->assertEquals(2, $results[1]);
        $this->assertEquals(3, $results[2]);
    }

    public function testActiveUsers()
    {
        $ui = UserInfo::getByID(2);
        $ui->deactivate();

        $this->assertEquals(2, $this->list->getTotalResults());
        $this->list->includeInactiveUsers();
        $this->assertEquals(3, $this->list->getTotalResults());
        $this->list->filterByIsActive(0);
        $this->assertEquals(1, $this->list->getTotalResults());
    }
}
