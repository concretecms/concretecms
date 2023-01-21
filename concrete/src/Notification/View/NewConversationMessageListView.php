<?php
namespace Concrete\Core\Notification\View;


use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Conversation\Message\Author;
use Concrete\Core\Entity\Notification\NewConversationMessageNotification;
use Concrete\Core\Notification\ContextMenu\Item\ArchiveItem;
use HtmlObject\Element;

class NewConversationMessageListView extends StandardListView
{

    /**
     * @var NewConversationMessageNotification
     */
    protected $notification;

    public function getTitle()
    {
        $message = $this->notification->getConversationMessageObject();
        if ($message && !$message->isConversationMessageApproved()) {
            return t('New Pending Conversation Message');
        }
        return t('New Conversation Message');
    }

    public function getIconClass()
    {
        return 'fas fa-comment';
    }

    public function getInitiatorUserObject()
    {
        $message = $this->notification->getConversationMessageObject();
        if (is_object($message)) {
            $author = $message->getConversationMessageAuthorObject();
            if ($author) {
                /**
                 * @var $author Author
                 */
                return $author->getUser();
            }
        }
    }

    public function getActionDescription()
    {
        $message = $this->notification->getConversationMessageObject();
        return $message->getConversationMessageBodyOutput(true);
    }

    protected function getRequestedByElement()
    {
        return new Element('span', t('Posted By '));
    }

    public function getMenu()
    {
        $menu = new DropdownMenu();
        $item = new LinkItem(
            app('url')->to('/dashboard/conversations/messages'),
            t('Manage Conversations')
        );
        $menu->addItem($item);
        $menu->addItem(new DividerItem());
        $menu->addItem(new ArchiveItem());
        return $menu;

    }


}
