<?php
namespace Concrete\Core\Authentication\OAuth2\Storage;

use Hautelook\Phpass\PasswordHash;

class Concrete extends \OAuth2\Storage\Pdo
{

    protected function checkPassword($user, $password)
    {
        $hash = \Database::connection()->fetchColumn("SELECT uPassword FROM Users WHERE uName=?", array($user['user_id']));

        $hasher = new PasswordHash(
            \Config::get('concrete.user.password.hash_cost_log2'),
            \Config::get('concrete.user.password.hash_portable'));

        return $hasher->checkPassword($password, $hash);
    }

    public function getUser($username)
    {
        $user = \UserInfo::getByUserName($username);
        if ($user) {
            return array(
                'user_id' => $username,//$user->getUserID(),
                'username' => $username
            );
        }
    }

}