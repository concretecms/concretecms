<?php
namespace Concrete\Controller\SinglePage\Dashboard\Users\Groups;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\User\User;
use Concrete\Core\Validation\CSRF\Token;

class Message extends DashboardPageController
{
    public function view()
    {
        $groups = [];
        $groupList = new GroupList();
        foreach ($groupList->getResults() as $group) {
            /** @var \Concrete\Core\User\Group\Group $group */
            $groups[$group->getGroupID()] = $group->getGroupName();
        }

        $this->set('groups', $groups);
        $this->set('token', new Token());
    }

    public function process()
    {
        if (!$this->token->validate('send_message')){
            $this->error->add(t('Invalid CSRF token. Please refresh and try again.'));
            $this->view();
            return null;
        }

        $group = Group::getByID($this->post('group'));
        if (!$group) {
            $this->error->add(t('Group not found.'));
            $this->view();
            return null;
        }

        if (!$group->getGroupMembers()) {
            $this->error->add(t('Message not sent. Group has no members.'));
            $this->view();
            return null;
        }

        if (!$this->post('subject')) {
            $this->error->add(t('Subject cannot be empty.'));
            $this->view();
            return null;
        }

        if (!$this->post('message')) {
            $this->error->add(t('Message cannot be empty.'));
            $this->view();
            return null;
        }

        $subject = $this->post('subject');
        $text = $this->post('message');

        $u = new User();
        $sender = $u->getUserInfoObject();

        if (!$sender) {
            throw new \RuntimeException('User must be logged in.');
        }

        foreach ($group->getGroupMembers() as $member) {
            $sender->sendPrivateMessage($member, $subject, $text);
        }

        $this->flash('success', t('Message Successfully sent.'));
        $this->view();
        return null;
    }
}
