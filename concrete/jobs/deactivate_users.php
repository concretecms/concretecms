<?php
namespace Concrete\Job;

use Concrete\Core\Entity\User\User;
use Concrete\Core\User\Event\DeactivateUser;
use Concrete\Core\User\UserInfo;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Job as AbstractJob;
use Core;
use Concrete\Core\User\UserInfoRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeactivateUsers extends AbstractJob
{

    /**
     * The event dispatcher we use to report that a user is being deactivated
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->dispatcher = $eventDispatcher;
    }

    public function getJobName()
    {
        return t("Deactivate Users");
    }

    public function getJobDescription()
    {
        return t("Deactivates users who haven't logged in recently, if automatic user deactivation is active.");
    }

    public function run()
    {
        $app = Core::make('app');
        $config = $app->make('config');
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
            return t2('%s user deactivated', '%s users deactivated', $users);
        } else {
            return t('Automatic user deactivation is disabled. Job aborted.');
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
