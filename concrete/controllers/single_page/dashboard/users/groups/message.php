<?php /** @noinspection PhpDeprecationInspection */

namespace Concrete\Controller\SinglePage\Dashboard\Users\Groups;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\User\PrivateMessage\Limit;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\UserInfoRepository;
use Concrete\Core\Validation\CSRF\Token;
use RuntimeException;

class Message extends DashboardPageController
{
    /**
     * ID used to send to all groups.
     */
    const ALL_GROUPS_ID = -2;

    /**
     * @var UserInfoRepository
     */
    private $repository;

    public function __construct(Page $c, UserInfoRepository $repository)
    {
        parent::__construct($c);
        $this->repository = $repository;
    }

    /**
     * Main endpoint for this singlepage.
     */
    public function view()
    {
        // Initialize groups array with an option to send to all groups
        $groups = [self::ALL_GROUPS_ID => t('All Groups')];

        $groupList = new GroupList();

        foreach ($groupList->getResults() as $group) {
            /** @var Group $group */
            $groups[$group->getGroupID()] = $group->getGroupDisplayName();
        }

        $this->set('groups', $groups);
        $this->set('token', new Token());
    }

    /**
     * Handle actually sending out the emails.
     *
     * @return RedirectResponse|void
     */
    public function process()
    {
        $subject = trim($this->post('subject'));
        $body = trim($this->post('message'));

        // Do some quick validation
        if (!$this->token->validate('send_message')) {
            $this->error->add(t('Invalid CSRF token. Please refresh and try again.'));
            return $this->view();
        }

        if (!$subject) {
            $this->error->add(t('Subject cannot be empty.'));
        }

        if (!$body) {
            $this->error->add(t('Message cannot be empty.'));
        }

        // Resolve the recipients
        $recipients = $this->getRecipients();

        // Make sure we have at least one recipient
        if ($recipients !== null && !$recipients) {
            $this->error->add(t('Message not sent. Group has no members.'));
        }

        // If we have errors, just return
        if ($this->error->has()) {
            return $this->view();
        }

        // Send out the message
        $this->sendMessage($subject, $body, $recipients, $this->getSender());

        $this->flash('success', t('Message Successfully sent.'));

        return Redirect::to('/dashboard/users/groups/message');
    }

    /**
     * Send a message to a list of recipients with limit disabled.
     *
     * @param $subject
     * @param $body
     * @param UserInfo[] $recipients
     * @param UserInfo $sender
     */
    protected function sendMessage($subject, $body, array $recipients, UserInfo $sender)
    {
        // Ignore limit instead of queueing

        // @TODO Queue private messages rather than ignoring the limit
        Limit::setEnabled(false);

        foreach ($recipients as $member) {
            $sender->sendPrivateMessage($member, $subject, $body);
        }

        // Reenable limit
        Limit::setEnabled();
    }

    /**
     * Get the user that should be the sender.
     *
     * @return UserInfo|null
     */
    protected function getSender()
    {
        $u = $this->app->make(User::class);
        $sender = $u->getUserInfoObject();
        if (!$sender) {
            throw new RuntimeException('User must be logged in.');
        }

        return $sender;
    }

    /**
     * Get the recipients.
     *
     * @return UserInfo[]
     */
    protected function getRecipients()
    {
        $groupId = (int)$this->post('group');

        // If the groupID is set to the "All" value, just get all active users
        if ($groupId === self::ALL_GROUPS_ID) {
            return $this->repository->all(true);
        }

        // Otherwise resolve the users from the group
        $group = Group::getByID($groupId);

        if (!$group) {
            $this->error->add(t('Group not found.'));

            return null;
        }

        return $group->getGroupMembers();
    }
}
