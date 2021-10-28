<?php
namespace Concrete\Core\Notification\Type;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Manager as CoreManager;

defined('C5_EXECUTE') or die("Access Denied.");

class Manager extends CoreManager
{
    public function createCoreUpdateDriver()
    {
        return $this->app->make('Concrete\Core\Notification\Type\CoreUpdateType');
    }

    /**
     * Create a user deactivated type
     *
     * @return \Concrete\Core\Notification\Type\UserDeactivatedType
     */
    public function createUserDeactivatedDriver()
    {
        return $this->app->make(UserDeactivatedType::class);
    }

    public function createNewConversationMessageDriver()
    {
        return $this->app->make('Concrete\Core\Notification\Type\NewConversationMessageType');
    }

    public function createNewFormSubmissionDriver()
    {
        return $this->app->make('Concrete\Core\Notification\Type\NewFormSubmissionType');
    }

    public function createNewPrivateMessageDriver()
    {
        return $this->app->make('Concrete\Core\Notification\Type\NewPrivateMessageType');
    }

    public function createUserSignupDriver()
    {
        return $this->app->make('Concrete\Core\Notification\Type\UserSignupType');
    }

    public function createWorkflowProgressDriver()
    {
        return $this->app->make('Concrete\Core\Notification\Type\WorkflowProgressType');
    }

    public function createGroupSignupRequestDriver()
    {
        return $this->app->make('Concrete\Core\Notification\Type\GroupSignupRequestType');
    }

    public function createGroupSignupDriver()
    {
        return $this->app->make('Concrete\Core\Notification\Type\GroupSignupType');
    }

    public function createGroupRoleChangeDriver()
    {
        return $this->app->make('Concrete\Core\Notification\Type\GroupRoleChangeType');
    }

    public function createGroupCreateDriver()
    {
        return $this->app->make('Concrete\Core\Notification\Type\GroupCreateType');
    }

    public function createGroupSignupRequestAcceptDriver()
    {
        return $this->app->make('Concrete\Core\Notification\Type\GroupSignupRequestAcceptType');
    }

    public function createGroupSignupRequestDeclineDriver()
    {
        return $this->app->make('Concrete\Core\Notification\Type\GroupSignupRequestDeclineType');
    }

    public function __construct(Application $application)
    {
        parent::__construct($application);
    }
}
