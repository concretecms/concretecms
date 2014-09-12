<?php
namespace Concrete\Controller\SinglePage\Dashboard\Conversations;

use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use stdClass;
use Page;
use UserInfo;
use \Concrete\Core\Conversation\FlagType\FlagType as ConversationFlagType;
use \Concrete\Core\Conversation\FlagType\FlagTypeList as ConversationFlagTypeList;
use \Concrete\Core\Conversation\Message\Message as ConversationMessage;
use \Concrete\Core\Conversation\Message\MessageList as ConversationMessageList;

class Messages extends DashboardPageController
{

    public function view()
    {
        $ml = new ConversationMessageList();
        $ml->setItemsPerPage(20);
        $cmpFilterTypes = array(
            'approved' => t('Approved'),
            'deleted' => t('Deleted'),
            'unapproved' => t('Unapproved')
        );
        $fl = new ConversationFlagTypeList();
        foreach ($fl->get() as $flagtype) {
            $cmpFilterTypes[$flagtype->getConversationFlagTypeHandle()] = Loader::helper('text')->unhandle(
                $flagtype->getConversationFlagTypeHandle()
            );
        }
        $cmpSortTypes = array(
            'date_desc' => t('Recent First'),
            'date_asc' => t('Earliest First')
        );

        if ($_REQUEST['cmpMessageKeywords']) {
            $ml->filterByKeywords($_REQUEST['cmpMessageKeywords']);
            $ml->filterByNotDeleted();
        }
        if ($_REQUEST['cmpMessageFilter'] && $_REQUEST['cmpMessageFilter'] != 'approved') {
            switch ($_REQUEST['cmpMessageFilter']) {
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
        } else {
            $ml->filterByApproved();
        }
        if ($_REQUEST['cmpMessageSort'] == 'date_asc') {
            $ml->sortByDateAscending();
        } else {
            $ml->sortByDateDescending();
        }

        $this->set('list', $ml);
        $this->set('messages', $ml->getPage());
        $this->set('cmpFilterTypes', $cmpFilterTypes);
        $this->set('cmpSortTypes', $cmpSortTypes);
    }

    public function bulk_update()
    {
        foreach ($this->post('cnvMessageID') as $messageID) {
            $messageObj = ConversationMessage::getByID($messageID);
            switch ($this->post('bulkTask')) {
                case 'Deleted':
                    $messageObj->delete();
                    break;
                case 'Spam':
                    $spamFlag = ConversationFlagType::getByHandle('spam');
                    $messageObj->flag($spamFlag);
                    break;
                case 'Unapproved':
                    $messageObj->unapprove();
                    break;
                case 'Approved':
                    $messageObj->approve();
                    break;
            }
        }
        $this->redirect(Page::getCurrentPage()->getCollectionPath());
    }

    public function unapprove()
    {
        $json = Loader::helper('json');
        $response = new stdClass();
        $message = ConversationMessage::getByID($this->post('messageID'));
        if (is_object($message)) {
            $message->unapprove();
            $response->success = t('Message successfully unapproved.');
            echo $json->encode($response);
            exit;
        } else {
            $response->error = t('Invalid message');
            echo $json->encode($response);
            exit;
        }
        exit;
    }

    public function approve()
    {
        $json = Loader::helper('json');
        $response = new stdClass();
        $message = ConversationMessage::getByID($this->post('messageID'));
        if (is_object($message)) {
            $message->approve();
            $response->success = t('Message successfully approved.');
            echo $json->encode($response);
            exit;
        } else {
            $response->error = t('Invalid message');
            echo $json->encode($response);
            exit;
        }
        exit;
    }

    public function deleteMessage()
    {
        $json = Loader::helper('json');
        $response = new stdClass();
        $message = ConversationMessage::getByID($this->post('messageID'));
        if (is_object($message)) {
            $message->delete();
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

    public function markSpam()
    {
        $json = Loader::helper('json');
        $response = new stdClass();
        $spamFlag = ConversationFlagType::getByHandle('spam');
        $message = ConversationMessage::getByID($this->post('messageID'));
        if (is_object($message)) {
            $message->flag($spamFlag);
            $response->success = t('Message successfully marked as spam.');
            echo $json->encode($response);
            exit;
        } else {
            $response->error = t('Invalid message');
            echo $json->encode($response);
            exit;
        }
        exit;
    }

    public function unmarkSpam()
    {
        $json = Loader::helper('json');
        $response = new stdClass();
        $spamFlag = ConversationFlagType::getByHandle('spam');
        $message = ConversationMessage::getByID($this->post('messageID'));
        if (is_object($message)) {
            $message->unflag($spamFlag);
            $response->success = t('Message successfully unmarked as spam.');
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

}