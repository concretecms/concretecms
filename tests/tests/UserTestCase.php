<?php

use Concrete\Core\Attribute\Key\Category;

abstract class UserTestCase extends ConcreteDatabaseTestCase
{
    protected $fixtures = array();
    protected $tables = array(
        'UserGroups', 'Groups',
        'TreeTypes', 'TreeNodes', 'TreeNodePermissionAssignments',
        'PermissionKeyCategories', 'PermissionKeys', 'TreeNodeTypes', 'Trees',
        'TreeGroupNodes',
    ); // so brutal

    protected $metadatas = array(
        'Concrete\Core\Entity\User\User',
        'Concrete\Core\Entity\User\UserSignup',
        'Concrete\Core\Entity\Attribute\Category',
        'Concrete\Core\Entity\Attribute\Key\Key',
        'Concrete\Core\Entity\Attribute\Key\UserValue',
        'Concrete\Core\Entity\Attribute\Key\UserKey',
    );

    protected function setUp()
    {
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
