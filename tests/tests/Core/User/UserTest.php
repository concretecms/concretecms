<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 6/28/14
 * Time: 10:30 AM
 */

namespace Concrete\Tests\Core\User;
use Core;

class UserTest extends \UserTestCase
{
    public function testCreateLegacy()
    {
        $ui = \UserInfo::add(array(
            'uName' => 'andrew',
            'uEmail' => 'andrew@concrete5.org'
        ));
        $this->assertEquals(1, $ui->getUserID());
        $this->assertEquals('andrew', $ui->getUserName());
        $this->assertEquals('andrew@concrete5.org', $ui->getUserEmail());


        $ui = \Concrete\Core\User\UserInfo::add(array(
            'uName' => 'andrew2',
            'uEmail' => 'andrew2@concrete5.org'
        ));
        $this->assertEquals(2, $ui->getUserID());
        $this->assertEquals('andrew2', $ui->getUserName());
        $this->assertEquals('andrew2@concrete5.org', $ui->getUserEmail());
    }

    public function testRegisterLegacy()
    {
        $ui = \UserInfo::register(array(
            'uName' => 'andrew',
            'uEmail' => 'andrew@concrete5.org'
        ));
        $this->assertEquals(1, $ui->getUserID());
        $this->assertEquals('andrew', $ui->getUserName());
        $this->assertEquals('andrew@concrete5.org', $ui->getUserEmail());
    }


    public function testCreateSuperUserLegacy()
    {
        $ui = \UserInfo::addSuperUser('test', 'andrew@concrete5.org');
        $this->assertEquals(USER_SUPER_ID, $ui->getUserID());
        $this->assertEquals(USER_SUPER, $ui->getUserName());
        $this->assertEquals('andrew@concrete5.org', $ui->getUserEmail());
    }

    public function testCreateNew()
    {
        $service = Core::make('user.registration');
        $ui = $service->create(array('uName' => 'andrew', 'uEmail' => 'andrew@concrete5.org'));
        $this->assertEquals(1, $ui->getUserID());
        $this->assertEquals('andrew', $ui->getUserName());
        $this->assertEquals('andrew@concrete5.org', $ui->getUserEmail());
    }
}
 