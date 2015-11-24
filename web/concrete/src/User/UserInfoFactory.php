<?php

namespace Concrete\Core\User;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\DatabaseManager;

class UserInfoFactory
{

    protected $connection;
    protected $application;

    public function __construct(Application $application, Connection $connection)
    {
        $this->application = $application;
        $this->connection = $connection;
    }

    /**
     * Returns the UserInfo object for a give user's uID.
     *
     * @param int $uID
     *
     * @return UserInfo|null
     */
    public function getByID($uID)
    {
        return $this->get('where uID = ?', $uID);
    }

    /**
     * Returns the UserInfo object for a give user's username.
     *
     * @param string $uName
     *
     * @return UserInfo|null
     */
    public function getByName($uName)
    {
        return $this->get('where uName = ?', $uName);
    }

    /**
     * @deprecated
     */
    public function getByUserName($uName)
    {
        return $this->getByName($uName);
    }

    /**
     * Returns the UserInfo object for a give user's email address.
     *
     * @param string $uEmail
     *
     * @return UserInfo|null
     */
    public function getByEmail($uEmail)
    {
        return $this->get('where uEmail = ?', $uEmail);
    }

    /**
     * @param string $uHash
     * @param bool $unredeemedHashesOnly
     *
     * @return UserInfo|null
     */
    public function getByValidationHash($uHash, $unredeemedHashesOnly = true)
    {
        $db = $this->connection;
        if ($unredeemedHashesOnly) {
            $uID = $db->fetchColumn("select uID from UserValidationHashes where uHash = ? and uDateRedeemed = 0", array($uHash));
        } else {
            $uID = $db->fetchColumn("select uID from UserValidationHashes where uHash = ?", array($uHash));
        }
        if ($uID) {
            $ui = self::getByID($uID);
            return $ui;
        }
    }

    private function get($where, $var)
    {
        $q = "select * from Users {$where}";
        $r = $this->connection->query($q, array($var));
        if ($r && $r->numRows() > 0) {
            $row = $r->fetchRow();
            $r->free();
            $ui = $this->application->make('Concrete\Core\User\UserInfo');
            $ui = array_to_object($ui, $row);
            return $ui;
        }
    }



}
