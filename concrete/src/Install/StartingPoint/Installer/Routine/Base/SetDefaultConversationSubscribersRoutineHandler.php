<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Application\Application;
use Concrete\Core\Conversation\Conversation;
use Concrete\Core\User\UserInfoRepository;

class SetDefaultConversationSubscribersRoutineHandler
{

    /**
     * @var UserInfoRepository
     */
    protected $userInfoRepository;

    public function __construct(UserInfoRepository $userInfoRepository, Application $app)
    {
        $this->userInfoRepository = $userInfoRepository;
    }

    public function __invoke()
    {
        $superUser = $this->userInfoRepository->getByID(USER_SUPER_ID);
        Conversation::setDefaultSubscribedUsers([$superUser]);
    }


}
