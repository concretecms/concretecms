<?php
namespace Concrete\Core\User\Avatar;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\DatabaseManager;
use Concrete\Core\User\UserInfo;

class AvatarService implements AvatarServiceInterface
{

    protected $connection;
    protected $application;

    public function __construct(Application $application, Connection $connection)
    {
        $this->connection = $connection;
        $this->application = $application;
    }

    public function userHasAvatar(UserInfo $ui)
    {
        return !!$this->connection->fetchColumn('select uHasAvatar from Users where uID = ?',
            array($ui->getUserID()));
    }

    public function removeAvatar(UserInfo $ui)
    {
        $this->connection->update('Users', array('uHasAvatar' => 0), array('uID' => $ui->getUserID()));
    }

    public function getAvatar(UserInfo $ui)
    {
        if ($this->userHasAvatar($ui)) {
            return $this->application->make('Concrete\Core\User\Avatar\StandardAvatar', array($ui));
        } else if ($this->application['config']->get('concrete.user.gravatar.enabled')) {
            return $this->application->make('Concrete\Core\User\Avatar\Gravatar', array($ui));
        } else {
            return $this->application->make('Concrete\Core\User\Avatar\EmptyAvatar', array($ui));
        }
    }

}
