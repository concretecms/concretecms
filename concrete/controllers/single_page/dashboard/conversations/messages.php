<?php
namespace Concrete\Controller\SinglePage\Dashboard\Conversations;

use Concrete\Core\Application\EditResponse;
use Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use stdClass;
use UserInfo;
use Core;
use Concrete\Core\Conversation\FlagType\FlagType as ConversationFlagType;
use Concrete\Core\Conversation\FlagType\FlagTypeList as ConversationFlagTypeList;
use Concrete\Core\Conversation\Message\Message as ConversationMessage;
use Concrete\Core\Conversation\Message\MessageList as ConversationMessageList;

class Messages extends DashboardPageController
{
    /**
     * Returns default message filter for search interface. We default to all, UNLESS we have at least one access
     * entity that publishes its messages and has them be unapproved. If that's the case, then we default to unapproved.
     */
    protected function getDefaultMessageFilter()
    {
        $filter = 'all';
        $db = \Database::get();
        $count = $db->GetOne('select count(cpa.paID) from ConversationPermissionAssignments cpa
            inner join PermissionAccess pa on cpa.paID = pa.paID
            inner join ConversationPermissionAddMessageAccessList cpl on pa.paID = cpl.paID
            where paIsInUse = 1 and permission = "U"');
        if ($count > 0) {
            $filter = 'unapproved';
        }

        return $filter;
    }

    public function view()
    {
        $ml = new ConversationMessageList();
        $ml->setItemsPerPage(20);
        $cmpFilterTypes = array(
            'all' => t('Show All'),
            'unapproved' => t('Unapproved'),
            'approved' => t('Approved'),
            'deleted' => t('Deleted'),
        );
        $fl = new ConversationFlagTypeList();
        foreach ($fl->get() as $flagtype) {
            $cmpFilterTypes[$flagtype->getConversationFlagTypeHandle()] = Loader::helper('text')->unhandle(
                $flagtype->getConversationFlagTypeHandle()
            );
        }

        if ($_REQUEST['cmpMessageKeywords']) {
            $ml->filterByKeywords($_REQUEST['cmpMessageKeywords']);
            $ml->filterByNotDeleted();
        }

        $cmpMessageFilter = $this->getDefaultMessageFilter();
        if ($this->request->query->has('cmpMessageFilter')
            && in_array($this->request->query->get('cmpMessageFilter'), array_keys($cmpFilterTypes))) {
            $cmpMessageFilter = $this->request->query->get('cmpMessageFilter');
        }

        switch ($cmpMessageFilter) {
            case 'all':
                break;
            case 'approved':
                $ml->filterByApproved();
                break;
            case 'deleted':
                $ml->filterByDeleted();
                break;
            case 'unapproved':
                $ml->filterByUnapproved();
                $ml->filterByNotDeleted();
                break;
            default: // flag
                $flagtype = ConversationFlagType::getByHandle($_REQUEST['cmpMessageFilter']);
                if (is_object($flagtype)) {
                    $ml->filterByFlag($flagtype);
                    $ml->filterByNotDeleted();
                } else {
                    $ml->filterByNotDeleted();
                }
                break;

        }

        $ml->sortByDateDescending();

        $this->set('list', $ml);
        $this->set('messages', $ml->getPage());
        $this->set('cmpFilterTypes', $cmpFilterTypes);
        $this->set('cmpMessageFilter', $cmpMessageFilter);
    }

    public function approve_message()
    {
        $e = Core::make('error');
        $message = ConversationMessage::getByID($this->post('cnvMessageID'));
        if (!is_object($message)) {
            $e->add(t('Invalid message'));
        } else {
            $mp = new \Permissions($message);
            if (!$mp->canApproveConversationMessage()) {
                $e->add(t('You do not have permission to approve this message.'));
            }
        }
        $er = new EditResponse($e);
        if (!$e->has()) {
            $message->approve();
            $er->setMessage(t('Message approved.'));
        }
        $er->outputJSON();
    }

    public function unflag_message()
    {
        $e = Core::make('error');
        $message = ConversationMessage::getByID($this->post('cnvMessageID'));
        if (!is_object($message)) {
            $e->add(t('Invalid message'));
        } else {
            $mp = new \Permissions($message);
            if (!$mp->canFlagConversationMessage()) {
                $e->add(t('You do not have permission to flag this message.'));
            }
        }
        $er = new EditResponse($e);
        if (!$e->has()) {
            $spamFlag = ConversationFlagType::getByHandle('spam');
            $message->unflag($spamFlag);
            $er->setMessage(t('Message unflagged.'));
        }
        $er->outputJSON();
    }

    public function undelete_message()
    {
        $e = Core::make('error');
        $message = ConversationMessage::getByID($this->post('cnvMessageID'));
        if (!is_object($message)) {
            $e->add(t('Invalid message'));
        } else {
            $mp = new \Permissions($message);
            if (!$mp->canDeleteConversationMessage()) {
                $e->add(t('You do not have permission to restore this message.'));
            }
        }
        $er = new EditResponse($e);
        if (!$e->has()) {
            $message->restore();
            $er->setMessage(t('Message restored.'));
        }
        $er->outputJSON();
    }

    /*

    public function restoreMessage()
    {
        $json = Loader::helper('json');
        $response = new stdClass();
        $message = ConversationMessage::getByID($this->post('messageID'));
        if (is_object($message)) {
            $message->restore();
            $response->success = t('Message successfully deleted.');
            echo $json->encode($response);
            exit;
        } else {
            $response->error = t('Invalid message');
            echo $json->encode($response);
            exit;
        }
        exit;
    }

    public function markUser()
    {
        $json = Loader::helper('json');
        $response = new stdClass();
        $message = ConversationMessage::getByID($this->post('messageID'));
        if (is_object($message)) {
            $targetUser = UserInfo::getByID($message->uID);
            if (is_object($targetUser)) {
                $userMessageList = new ConversationMessageList();
                $userMessageList->filterByUser($message->uID);
                $userMessages = $userMessageList->get();
                $spamFlag = ConversationFlagType::getByHandle('spam');
                foreach ($userMessages as $userMessage) {
                    $userMessage->flag($spamFlag);
                }
                $response->success = t('All user messages marked as spam.');
                echo $json->encode($response);
                exit;
            }
            $response->error = t('Invalid User');
            echo $json->encode($response);
            exit;
        } else {
            $response->error = t('Invalid message');
            echo $json->encode($response);
            exit;
        }
    }

    public function deactivateUser()
    {
        // notes -- do we want to check for posts by this user that are still active before deactivation?
        $json = Loader::helper('json');
        $response = new stdClass();
        $message = ConversationMessage::getByID($this->post('messageID'));
        if (is_object($message)) {
            $targetUser = UserInfo::getByID($message->uID);
            if (is_object($targetUser)) {
                $targetUser->deactivate();
                $response->success = t('User deactivated.');
                echo $json->encode($response);
                exit;
            }
            $response->error = t('Invalid User');
            echo $json->encode($response);
            exit;
        } else {
            $response->error = t('Invalid message');
            echo $json->encode($response);
            exit;
        }
    }

    public function blockUserIP()
    {
        $json = Loader::helper('json');
        $ip = Loader::helper('validation/ip');
        $response = new stdClass();
        $message = ConversationMessage::getByID($this->post('messageID'));
        if (is_object($message)) {
            $targetIP = $message->getConversationMessageSubmitIP();
            $ip->createIPBan($targetIP, true);
            $response->success = t('IP successfully banned.');
            echo $json->encode($response);
            exit;
        } else {
            $response->error = t('Invalid message');
            echo $json->encode($response);
            exit;
        }
        exit;
    }
    */
}
