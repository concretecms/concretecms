<?php
namespace Concrete\Job;

use Concrete\Core\User\Group\GroupList;
use Loader;
use QueueableJob;
use Group;
use User;
use \ZendQueue\Queue as ZendQueue;
use \ZendQueue\Message as ZendQueueMessage;

class CheckAutomatedGroups extends QueueableJob
{

    public $jSupportsQueue = true;

    public function getJobName()
    {
        return t("Check Automated Groups");
    }

    public function getJobDescription()
    {
        return t("Automatically add users to groups and assign badges.");
    }

    public function start(ZendQueue $q)
    {
        $db = Loader::db();
        $r = $db->Execute('select Users.uID from Users where uIsActive = 1 order by uID asc');
        while ($row = $r->FetchRow()) {
            $q->send($row['uID']);
        }
    }

    public function finish(ZendQueue $q)
    {
        return t('Active users updated.');
    }

    public function processQueueItem(ZendQueueMessage $msg)
    {
        $ux = User::getByUserID($msg->body);
        $groupControllers = Group::getAutomatedOnJobRunGroupControllers($ux);
        foreach ($groupControllers as $ga) {
            if ($ga->check($ux)) {
                $ux->enterGroup($ga->getGroupObject());
            }
        }

        $gl = new GroupList();
        $gl->filterByExpirable();
        $groups = $gl->getResults();
        foreach ($groups as $group) {
            if ($group->isUserExpired($ux)) {
                $ux->exitGroup($group);
            }
        }
    }
}
