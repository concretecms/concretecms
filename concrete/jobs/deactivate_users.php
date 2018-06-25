<?php
namespace Concrete\Job;

use Concrete\Core\User\UserInfo;
use Job as AbstractJob;
use Core;
use Concrete\Core\User\UserInfoRepository;

class DeactivateUsers extends AbstractJob
{
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
            $timestamp = $now->getTimestamp();

            // At this point $timestamp is set to the cut off point: accounts within login timestamps older than
            // $timestamp will be deactivated.

            $db = $app->make('database');
            $repository = $app->make(UserInfoRepository::class);

            $r = $db->executeQuery('select uID from Users where uIsActive = 1 and uLastLogin < ?', [$timestamp]);
            $users = 0;
            while ($row = $r->fetch()) {
                $ui = $repository->getByID($row['uID']);
                if ($ui) {
                    $users++;
                    $ui->deactivate();
                }
            }
            return t2('1 user deactivated', '%s users deactivated', $users);
        } else {
            return t('Automatic user deactivation is disabled. Job aborted.');
        }
    }
}
