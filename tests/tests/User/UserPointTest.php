<?php

namespace Concrete\Tests\User;

use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Point\Action\Action;
use Concrete\Core\User\Point\Action\WonBadgeAction;
use Concrete\Core\User\Point\EntryList;
use Concrete\Core\User\UserInfo;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

class UserPointTest extends ConcreteDatabaseTestCase
{
    protected $tables = ['UserPointActions', 'Groups', 'TreeTypes', 'Trees', 'TreeNodes', 'TreeGroupNodes',
    'UserGroups', 'UserPointHistory', 'PermissionKeys', 'PermissionKeyCategories', ];

    protected $metadatas = [
        'Concrete\Core\Entity\Site\Site',
        'Concrete\Core\Entity\Site\Locale',
        'Concrete\Core\Entity\Site\Type',
        'Concrete\Core\Entity\Site\Tree',
        'Concrete\Core\Entity\Site\SiteTree',
        'Concrete\Core\Entity\User\User',
        'Concrete\Core\Entity\User\UserSignup',
    ];

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // Add the won_badge action
        Action::add('won_badge', t('Won a Badge'), 5, false);
    }

    public function testUserPointActionWithGroup()
    {
        $g = \Group::add('Test Group', 'Test Group Description');

        Action::add('test_action_with_group', t('Test Action'), 4, $g);
        $action = Action::getByHandle('test_action_with_group');
        /* @var $action \Concrete\Core\User\Point\Action\Action */

        $this->assertInstanceOf(Action::class, $action);
        $this->assertEquals(4, $action->getUserPointActionDefaultPoints());
        $this->assertInstanceOf(Group::class, $action->getUserPointActionBadgeGroupObject());
        $this->assertEquals($g->getGroupID(), $action->getUserPointActionBadgeGroupObject()->getGroupID());
    }

    public function testAddingBadgeToUser()
    {
        \Cache::disableAll();
        \Site::installDefault();
        \Config::set('concrete.email.enabled', false);
        \Config::set('concrete.log.emails', false);
        $g = Group::add('Badge Group', 'Gettin a Badge');
        $g->setBadgeOptions(0, 'test', 10);

        // Pull the group we just made using the c5 api
        $g = Group::getByName('Badge Group');

        $user = UserInfo::add(
            ['uName' => 'testuser', 'uEmail' => 'testuser@concrete5.org']
        );

        $uo = $user->getUserObject();
        $uo->enterGroup($g);

        \Config::clear('concrete.email.enabled');
        \Config::clear('concrete.log.emails');

        $list = new EntryList();
        $list->filterbyUserName('testuser');
        $results = $list->get();

        $this->assertCount(1, $results);

        $result = $results[0];
        $this->assertInstanceOf('\Concrete\Core\User\Point\Entry', $result);

        /* @var $result \Concrete\Core\User\Point\Entry */
        $this->assertInstanceOf('\Concrete\Core\User\Point\Action\WonBadgeAction', $result->getUserPointEntryActionObject());
        $this->assertInstanceOf('\Concrete\Core\User\Point\Action\WonBadgeActionDescription', $result->getUserPointEntryDescriptionObject());
    }

    public function testUserPointActionWithNoGroup()
    {
        Action::add('test_action_with_no_group', t('Test Action'), 2, false);
        $action = Action::getByHandle('test_action_with_no_group');
        /* @var $action \Concrete\Core\User\Point\Action\Action */

        $this->assertEquals(2, $action->getUserPointActionDefaultPoints());
        $this->assertEquals(null, $action->getUserPointActionBadgeGroupObject());
    }

    public function testCustomUserPointAction()
    {
        /* @var $action \Concrete\Core\User\Point\Action\WonBadgeAction */
        $action = Action::getByHandle('won_badge');

        $this->assertTrue($action->hasCustomClass());
        $this->assertInstanceOf(WonBadgeAction::class, $action);
        $this->assertEquals(5, $action->getUserPointActionDefaultPoints());
        $this->assertEquals(null, $action->getUserPointActionBadgeGroupObject());
    }
}
