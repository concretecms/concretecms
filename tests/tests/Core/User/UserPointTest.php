<?php
namespace Concrete\Tests\Core\User;

use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Point\Action\Action;
use Concrete\Core\User\Point\EntryList;

class UserPointTest extends \ConcreteDatabaseTestCase
{
    protected $tables = array('UserPointActions', 'Groups', 'TreeTypes', 'Trees', 'TreeNodes', 'TreeGroupNodes',
    'Users', 'UserGroups', 'UserPointHistory', );

    public function testUserPointActionWithGroup()
    {
        $g = \Group::add('Test Group', 'Test Group Description');

        Action::add('test_action', t('Test Action'), 4, $g);
        $action = Action::getByHandle('test_action');
        /* @var $action \Concrete\Core\User\Point\Action\Action */

        $this->assertInstanceOf('\Concrete\Core\User\Point\Action\Action', $action);
        $this->assertEquals(4, $action->getUserPointActionDefaultPoints());
        $this->assertInstanceOf('\Concrete\Core\User\Group\Group', $action->getUserPointActionBadgeGroupObject());
        $this->assertEquals($g->getGroupID(), $action->getUserPointActionBadgeGroupObject()->getGroupID());
    }

    public function testUserPointActionWithNoGroup()
    {
        Action::add('test_action', t('Test Action'), 2, false);
        $action = Action::getByHandle('test_action');
        /* @var $action \Concrete\Core\User\Point\Action\Action */

        $this->assertEquals(2, $action->getUserPointActionDefaultPoints());
        $this->assertEquals(null, $action->getUserPointActionBadgeGroupObject());
    }

    public function testCustomUserPointAction()
    {
        Action::add('won_badge', t('Won a Badge'), 5, false);
        $action2 = Action::getByID(1);
        $action3 = Action::getByHandle('won_badge');

        /* @var $action2 \Concrete\Core\User\Point\Action\WonBadgeAction */
        /* @var $action3 \Concrete\Core\User\Point\Action\WonBadgeAction */
        $this->assertTrue($action2->hasCustomClass());
        $this->assertInstanceOf('\Concrete\Core\User\Point\Action\WonBadgeAction', $action2);
        $this->assertEquals(5, $action2->getUserPointActionDefaultPoints());
        $this->assertEquals(null, $action2->getUserPointActionBadgeGroupObject());
        $this->assertInstanceOf('\Concrete\Core\User\Point\Action\WonBadgeAction', $action3);
        $this->assertEquals(5, $action3->getUserPointActionDefaultPoints());
        $this->assertEquals(null, $action3->getUserPointActionBadgeGroupObject());
    }

    public function testAddingBadgeToUser()
    {
        \Cache::disableAll();
        \Config::set('concrete.email.enabled', false);
        \Config::set('concrete.log.emails', false);
        Action::add('won_badge', t('Won a Badge'), 5, false);
        $g = Group::add('Test Group', 'Gettin a Badge');
        $g->setBadgeOptions(0, 'test', 10);
        $g = Group::getByID(1);

        $user = \Concrete\Core\User\UserInfo::add(
            array('uName' => 'testuser', 'uEmail' => 'testuser@concrete5.org')
        );

        $uo = $user->getUserObject();
        $uo->enterGroup($g);

        \Config::clear('concrete.email.enabled');
        \Config::clear('concrete.log.emails');

        $list = new EntryList();
        $list->filterbyUserName('testuser');
        $results = $list->get();

        $this->assertEquals(1, count($results));

        $result = $results[0];
        $this->assertInstanceOf('\Concrete\Core\User\Point\Entry', $result);

        /* @var $result \Concrete\Core\User\Point\Entry */
        $this->assertInstanceOf('\Concrete\Core\User\Point\Action\WonBadgeAction', $result->getUserPointEntryActionObject());
        $this->assertInstanceOf('\Concrete\Core\User\Point\Action\WonBadgeActionDescription', $result->getUserPointEntryDescriptionObject());
    }
}
