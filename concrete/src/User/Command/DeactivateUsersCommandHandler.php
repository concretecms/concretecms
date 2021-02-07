<?php

namespace Concrete\Core\User\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\User\User;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\User\Event\DeactivateUser;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class DeactivateUsersCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    public function __construct(Application $app, Repository $config, EventDispatcher $dispatcher)
    {
        $this->app = $app;
        $this->config = $config;
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(DeactivateUsersCommand $command)
    {
        $app = $this->app;
        $config = $this->config;
        if ($config->get('concrete.user.deactivation.enable_login_threshold_deactivation')) {
            $threshold = (int) $config->get('concrete.user.deactivation.login.threshold');
            $now = new \DateTime();
            $now->sub(new \DateInterval('P' . $threshold . 'D'));
            $timestampDatetimeFormat = $now->format('Y-m-d H:i:s');
            $timestamp = $now->getTimestamp();

            // At this point $timestamp is set to the cut off point: accounts within login timestamps older than
            // $timestamp will be deactivated.

            $db = $app->make('database');

            /** @var EntityManagerInterface $em */
            $em = $app->make(EntityManagerInterface::class);
            $userRepository = $em->getRepository(User::class);
            $repository = $app->make(UserInfoRepository::class);

            $r = $db->executeQuery(
                'select uID from Users where uIsActive = 1 and uLastLogin < ? and uDateAdded < ?',
                [$timestamp, $timestampDatetimeFormat]
            );
            $users = 0;
            while ($row = $r->fetch()) {
                $id = (int) $row['uID'];

                try {
                    $user = $userRepository->find($id);
                } catch (EntityNotFoundException $e) {
                    continue;
                }

                $userInfo = $repository->getByID($id);

                if ($userInfo) {
                    $this->deactivateUser($user, $userInfo);
                    $users++;
                }
            }
            $this->output->write(t2('%s user deactivated', '%s users deactivated', $users));
        } else {
            $this->output->write(t('Automatic user deactivation is disabled. Task aborted.'));
        }
    }

    protected function deactivateUser(User $user, UserInfo $userInfo)
    {
        $event = DeactivateUser::create($user);
        $this->dispatcher->dispatch('on_before_user_deactivate', $event);

        $userInfo->deactivate();

        $this->dispatcher->dispatch('on_after_user_deactivate', $event);
    }


}