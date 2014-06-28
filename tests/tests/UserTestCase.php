<?php
define('ENABLE_BLOCK_CACHE', false);
use Concrete\Core\Attribute\Key\Category;
abstract class UserTestCase extends ConcreteDatabaseTestCase {

    protected $fixtures = array();
    protected $tables = array('Users', 'UserGroups', 'UserAttributeValues'); // so brutal

    public function setUp() {
        parent::setUp();
        Category::add('user');
    }
    protected static function createUser($uName, $uEmail)
    {
        $user = \Concrete\Core\User\UserInfo::add(
            array('uName' => $uName, 'uEmail' => $uEmail)
        );
        return $user;
    }

}