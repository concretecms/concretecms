<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 6/28/14
 * Time: 10:30 AM
 */

namespace Concrete\Tests\Core\User;
class UserListTest extends \UserTestCase {

    protected $userData = array(
        array(
            'testuser', 'testuser@concrete5.org',
        ),
        array(
            'testuser2', 'testuser2@concrete5.org'
        )
    );

    public function setUp()
    {
        parent::setUp();

        foreach($this->userData as $data) {
            $ui = call_user_func_array(array($this, 'createUser'), $data);
            $ui->reindex();
        }

        $this->list = new \Concrete\Core\User\UserList();
    }

    public function testTotal()
    {

    }

}
 