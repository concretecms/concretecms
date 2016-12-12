<?php

namespace Concrete\Core\User;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\DatabaseManager;
use Concrete\Core\User\Event\AddUser;
use Concrete\Core\User\Event\UserInfoWithPassword;
use Hautelook\Phpass\PasswordHash;

class RegistrationService implements RegistrationServiceInterface
{

    protected $connection;
    protected $application;
    protected $userInfoFactory;

    public function __construct(Application $application, Connection $connection, UserInfoFactory $userInfoFactory)
    {
        $this->application = $application;
        $this->connection = $connection;
        $this->userInfoFactory = $userInfoFactory;
    }

    /**
     * @param string $uPasswordEncrypted
     * @param string $uEmail
     * @return UserInfo|null
     */
    public function createSuperUser($uPasswordEncrypted, $uEmail)
    {
        $dh = $this->application->make('date');
        $uDateAdded = $dh->getOverridableNow();

        $v = array(USER_SUPER_ID, USER_SUPER, $uEmail, $uPasswordEncrypted, 1, $uDateAdded, $uDateAdded);
        $r = $this->connection->prepare("insert into Users (uID, uName, uEmail, uPassword, uIsActive, uDateAdded, uLastPasswordChange) values (?, ?, ?, ?, ?, ?, ?)");
        $res = $r->execute($v);
        if ($res) {
            $newUID = $this->connection->lastInsertId();
            return $this->userInfoFactory->getByID($newUID);
        }
    }

    /**
     * @param array $data
     * @return UserInfo|false|null
     */
    public function create($data)
    {
        $uae = new AddUser($data);
        $uae = \Events::dispatch('on_before_user_add', $uae);
        if (!$uae->proceed()) {
            return false;
        }

        $db = $this->connection;
        $dh = $this->application->make('date');
        $uDateAdded = $dh->getOverridableNow();
        $config = $this->application->make('config');
        $hasher = new PasswordHash($config->get('concrete.user.password.hash_cost_log2'), $config->get('concrete.user.password.hash_portable'));

        if (isset($data['uIsValidated']) && $data['uIsValidated'] == 1) {
            $uIsValidated = 1;
        } elseif (isset($data['uIsValidated']) && $data['uIsValidated'] == 0) {
            $uIsValidated = 0;
        } else {
            $uIsValidated = -1;
        }

        if (isset($data['uIsFullRecord']) && $data['uIsFullRecord'] == 0) {
            $uIsFullRecord = 0;
        } else {
            $uIsFullRecord = 1;
        }

        $password_to_insert = isset($data['uPassword']) ? $data['uPassword'] : null;
        $hash = $hasher->HashPassword($password_to_insert);

        $uDefaultLanguage = null;
        if (isset($data['uDefaultLanguage']) && $data['uDefaultLanguage'] != '') {
            $uDefaultLanguage = $data['uDefaultLanguage'];
        }
        $v = array($data['uName'], $data['uEmail'], $hash, $uIsValidated, $uDateAdded, $uDateAdded, $uIsFullRecord, $uDefaultLanguage, 1);
        $r = $db->prepare("insert into Users (uName, uEmail, uPassword, uIsValidated, uDateAdded, uLastPasswordChange, uIsFullRecord, uDefaultLanguage, uIsActive) values (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $res = $r->execute($v);
        if ($res) {
            $newUID = $db->Insert_ID();
            $ui = $this->userInfoFactory->getByID($newUID);

            if (is_object($ui)) {
                $uo = $ui->getUserObject();
                $groupControllers = \Group::getAutomatedOnRegisterGroupControllers($uo);
                foreach ($groupControllers as $ga) {
                    if ($ga->check($uo)) {
                        $uo->enterGroup($ga->getGroupObject());
                    }
                }

                // run any internal event we have for user add
                $ue = new UserInfoWithPassword($ui);
                $ue->setUserPassword($password_to_insert);
                \Events::dispatch('on_user_add', $ue);
            }

            return $ui;
        }
    }

    /**
     * @param array $data
     *
     * @return UserInfo
     */
    public function createFromPublicRegistration($data)
    {
        // slightly different than add. this is public facing
        $config = $this->application->make('config');
        if ($config->get('concrete.user.registration.validate_email')) {
            $data['uIsValidated'] = 0;
        }
        $ui = $this->create($data);
        return $ui;
    }


}
