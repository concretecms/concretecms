<?php

namespace Concrete\Core\User\Command;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\File\Rescanner;
use Concrete\Core\User\Command\CheckAutomatedGroupsCommand;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\User\UserInfoRepository;
use Doctrine\ORM\EntityManager;

class CheckAutomatedGroupsCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @var UserInfoRepository
     */
    protected $userInfoRepository;

    public function __construct(UserInfoRepository $userInfoRepository)
    {
        $this->userInfoRepository = $userInfoRepository;
    }

    public function __invoke(CheckAutomatedGroupsCommand $command)
    {
        $user = $this->userInfoRepository->getByID($command);
        if ($user) {
            $this->output->write(t('Checking user: %s (ID: %s)', $user->getUserName(), $user->getUserID()));
            $groupControllers = Group::getAutomatedOnJobRunGroupControllers($user);
            foreach ($groupControllers as $ga) {
                if ($ga->check($user)) {
                    $user->enterGroup($ga->getGroupObject());
                }
            }

            $gl = new GroupList();
            $gl->filterByExpirable();
            $groups = $gl->getResults();
            foreach ($groups as $group) {
                if ($group->isUserExpired($user)) {
                    $user->exitGroup($group);
                }
            }
        }
    }


}