<?php

namespace Concrete\Block\CoreConversation;

use Core;
use Database;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Conversation\Conversation;
use Concrete\Core\Conversation\Message\MessageList;
use Concrete\Core\Feature\ConversationFeatureInterface;
use Page;

/**
 * The controller for the conversation block. This block is used to display conversations in a page.
 *
 * @package Blocks
 * @subpackage Conversation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2013 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class Controller extends BlockController implements ConversationFeatureInterface
{
    protected $btCacheBlockRecord = true;
    protected $btTable = 'btCoreConversation';
    protected $conversation;
    protected $btWrapperClass = 'ccm-ui';
    protected $btCopyWhenPropagate = true;
    protected $btFeatures = array(
        'conversation',
    );

    public function getBlockTypeDescription()
    {
        return t("Displays conversations on a page.");
    }

    public function getBlockTypeName()
    {
        return t("Conversation");
    }

    public function getSearchableContent()
    {
        $ml = new MessageList();
        $ml->filterByConversation($this->getConversationObject());
        $messages = $ml->get();
        if (!count($messages)) {
            return '';
        }

        $content = '';
        foreach ($messages as $message) {
            $content .= $message->getConversationMessageSubject() . ' ' .
                       strip_tags($message->getConversationMessageBody()) . ' ';
        }

        return rtrim($content);
    }

    public function getConversationFeatureDetailConversationObject()
    {
        return $this->getConversationObject();
    }

    public function getConversationObject()
    {
        if (!isset($this->conversation)) {
            // i don't know why this->cnvid isn't sticky in some cases, leading us to query
            // every damn time
            $db = Database::get();
            $cnvID = $db->GetOne('select cnvID from btCoreConversation where bID = ?', array($this->bID));
            $this->conversation = Conversation::getByID($cnvID);
        }

        return $this->conversation;
    }

    public function duplicate_master($newBID, $newPage)
    {
        parent::duplicate($newBID);
        $db = Database::get();
        $conv = Conversation::add();
        $conv->setConversationPageObject($newPage);
        $this->conversation = $conv;
        $db->Execute('update btCoreConversation set cnvID = ? where bID = ?', array($conv->getConversationID(), $newBID));
    }

    public function edit()
    {
        $fileSettings = $this->getFileSettings();
        $this->set('maxFilesGuest', $fileSettings['maxFilesGuest']);
        $this->set('maxFilesRegistered', $fileSettings['maxFilesRegistered']);
        $this->set('maxFileSizeGuest', $fileSettings['maxFileSizeGuest']);
        $this->set('maxFileSizeRegistered', $fileSettings['maxFileSizeRegistered']);
        $this->set('fileExtensions', $fileSettings['fileExtensions']);
        $this->set('attachmentsEnabled', $fileSettings['attachmentsEnabled'] > 0 ? $fileSettings['attachmentsEnabled'] : '');
        $this->set('attachmentOverridesEnabled', $fileSettings['attachmentOverridesEnabled'] > 0 ? $fileSettings['attachmentOverridesEnabled'] : '');

        $conversation = $this->getConversationObject();
        $this->set('notificationOverridesEnabled', $conversation->getConversationNotificationOverridesEnabled());
        $this->set('subscriptionEnabled', $conversation->getConversationSubscriptionEnabled());
        $this->set('notificationUsers', $conversation->getConversationSubscribedUsers());
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('core/conversation');
        $this->requireAsset('core/lightbox');
        $u = new \User();
        if (!$u->isRegistered()) {
            $this->requireAsset('css', 'core/frontend/captcha');
        }
    }
    public function view()
    {
        $fileSettings = $this->getFileSettings();
        $conversation = $this->getConversationObject();
        if (is_object($conversation)) {
            $this->set('conversation', $conversation);
            if ($this->enablePosting) {
                $token = Core::make('helper/validation/token')->generate('add_conversation_message');
            } else {
                $token = '';
            }
            $this->set('posttoken', $token);
            $this->set('cID', Page::getCurrentPage()->getCollectionID());
            $this->set('users', $this->getActiveUsers(true));
            $this->set('maxFilesGuest', $fileSettings['maxFilesGuest']);
            $this->set('maxFilesRegistered', $fileSettings['maxFilesRegistered']);
            $this->set('maxFileSizeGuest', $fileSettings['maxFileSizeGuest']);
            $this->set('maxFileSizeRegistered', $fileSettings['maxFileSizeRegistered']);
            $this->set('fileExtensions', $fileSettings['fileExtensions']);
            $this->set('attachmentsEnabled', $fileSettings['attachmentsEnabled']);
            $this->set('attachmentOverridesEnabled', $fileSettings['attachmentOverridesEnabled']);
        }
    }

    public function getFileSettings()
    {
        $conversation = $this->getConversationObject();
        $helperFile = Core::make('helper/concrete/file');
        $maxFilesGuest = $conversation->getConversationMaxFilesGuest();
        $attachmentOverridesEnabled = $conversation->getConversationAttachmentOverridesEnabled();
        $maxFilesRegistered = $conversation->getConversationMaxFilesRegistered();
        $maxFileSizeGuest = $conversation->getConversationMaxFileSizeGuest();
        $maxFileSizeRegistered = $conversation->getConversationMaxFileSizeRegistered();
        $fileExtensions = $conversation->getConversationFileExtensions();
        $attachmentsEnabled = $conversation->getConversationAttachmentsEnabled();

        $fileExtensions = implode(',', $helperFile->unserializeUploadFileExtensions($fileExtensions)); //unserialize and implode extensions into comma separated string

        $fileSettings = array();
        $fileSettings['maxFileSizeRegistered'] = $maxFileSizeRegistered;
        $fileSettings['maxFileSizeGuest'] = $maxFileSizeGuest;
        $fileSettings['maxFilesGuest'] = $maxFilesGuest;
        $fileSettings['maxFilesRegistered'] = $maxFilesRegistered;
        $fileSettings['fileExtensions'] = $fileExtensions;
        $fileSettings['attachmentsEnabled'] = $attachmentsEnabled;
        $fileSettings['attachmentOverridesEnabled'] = $attachmentOverridesEnabled;

        return $fileSettings;
    }

    public function getActiveUsers($lower = false)
    {
        $cnv = $this->getConversationObject();
        $uobs = $cnv->getConversationMessageUsers();
        $users = array();
        foreach ($uobs as $user) {
            if ($lower) {
                $users[] = strtolower($user->getUserName());
            } else {
                $users[] = $user->getUserName();
            }
        }

        return $users;
    }

    public function save($post)
    {
        $helperFile = Core::make('helper/concrete/file');
        $db = Database::get();
        $cnvID = $db->GetOne('select cnvID from btCoreConversation where bID = ?', array($this->bID));
        if (!$cnvID) {
            $conversation = Conversation::add();
            $b = $this->getBlockObject();
            $xc = $b->getBlockCollectionObject();
            $conversation->setConversationPageObject($xc);
        } else {
            $conversation = Conversation::getByID($cnvID);
        }
        $values = $post + array(
            'attachmentOverridesEnabled' => null,
            'attachmentsEnabled' => null,
            'itemsPerPage' => null,
            'maxFilesGuest' => null,
            'maxFilesRegistered' => null,
            'maxFileSizeGuest' => null,
            'maxFileSizeRegistered' => null,
            'enableOrdering' => null,
            'enableCommentRating' => null,
            'notificationOverridesEnabled' => null,
            'subscriptionEnabled' => null,
            'fileExtensions' => null,
        );
        if ($values['attachmentOverridesEnabled']) {
            $conversation->setConversationAttachmentOverridesEnabled(intval($values['attachmentOverridesEnabled']));
        } else {
            $conversation->setConversationAttachmentOverridesEnabled(0);
        }
        if ($values['attachmentsEnabled']) {
            $conversation->setConversationAttachmentsEnabled(intval($values['attachmentsEnabled']));
        }
        if (!$values['itemsPerPage']) {
            $values['itemsPerPage'] = 0;
        }
        if ($values['maxFilesGuest']) {
            $conversation->setConversationMaxFilesGuest(intval($values['maxFilesGuest']));
        }
        if ($values['maxFilesRegistered']) {
            $conversation->setConversationMaxFilesRegistered(intval($values['maxFilesRegistered']));
        }
        if ($values['maxFileSizeGuest']) {
            $conversation->setConversationMaxFileSizeGuest(intval($values['maxFileSizeGuest']));
        }
        if ($values['maxFileSizeRegistered']) {
            $conversation->setConversationMaxFilesRegistered(intval($values['maxFileSizeRegistered']));
        }
        if (!$values['enableOrdering']) {
            $values['enableOrdering'] = 0;
        }
        if (!$values['enableCommentRating']) {
            $values['enableCommentRating'] = 0;
        }

        if ($values['notificationOverridesEnabled']) {
            $conversation->setConversationNotificationOverridesEnabled(true);
            $users = array();
            if (is_array($this->post('notificationUsers'))) {
                foreach ($this->post('notificationUsers') as $uID) {
                    $ui = \UserInfo::getByID($uID);
                    if (is_object($ui)) {
                        $users[] = $ui;
                    }
                }
            }
            $conversation->setConversationSubscribedUsers($users);
            $conversation->setConversationSubscriptionEnabled(intval($values['subscriptionEnabled']));
        } else {
            $conversation->setConversationNotificationOverridesEnabled(false);
            $conversation->setConversationSubscriptionEnabled(0);
        }

        if ($values['fileExtensions']) {
            $receivedExtensions = preg_split('{,}', strtolower($values['fileExtensions']), null, PREG_SPLIT_NO_EMPTY);
            $fileExtensions = $helperFile->serializeUploadFileExtensions($receivedExtensions);
            $conversation->setConversationFileExtensions($fileExtensions);
        }

        $values['cnvID'] = $conversation->getConversationID();
        parent::save($values);
    }
}
