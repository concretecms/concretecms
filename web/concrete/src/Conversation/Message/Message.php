<?php
namespace Concrete\Core\Conversation\Message;

use Concrete\Core\Conversation\FlagType\FlagType;
use Concrete\Core\Conversation\Rating\Type;
use Config;
use Concrete\Core\File\File;
use Concrete\Core\File\Set\Set as FileSet;
use Core;
use Loader;
use Concrete\Core\Conversation\Conversation;
use Concrete\Core\Conversation\Editor\Editor as ConversationEditor;
use \Concrete\Core\Foundation\Object;
use Concrete\Core\User\User;
use Concrete\Core\User\UserInfo;
use Concrete\Core\Utility\IPAddress;
use Events;

class Message extends Object implements \Concrete\Core\Permission\ObjectInterface
{
    protected $cnvMessageDateCreated;

    public function getConversationMessageID() {return $this->cnvMessageID;}
    public function getConversationMessageSubject() {return $this->cnvMessageSubject;}
    public function getConversationMessageBody() {return $this->cnvMessageBody;}
    public function getConversationMessageDateCreated() {return $this->cnvMessageDateCreated;}
    public function getConversationID() {return $this->cnvID;}
    public function getConversationEditorID() {return $this->cnvEditorID;}
    public function getConversationMessageLevel() {return $this->cnvMessageLevel;}
    public function getConversationMessageParentID() {return $this->cnvMessageParentID;}
    public function getConversationMessageSubmitIP() {return new IPAddress($this->cnvMessageSubmitIP, true);}
    public function getConversationMessageSubmitUserAgent() { return $this->cnvMessageSubmitUserAgent;}
    public function isConversationMessageDeleted() {return $this->cnvIsMessageDeleted;}
    public function isConversationMessageFlagged() {return (count($this->getConversationMessageFlagTypes()) > 0);}
    public function isConversationMessageApproved() {return $this->cnvIsMessageApproved;}
    public function getConversationMessageFlagTypes()
    {
        $db = Loader::db();
        if ($this->cnvMessageFlagTypes) return $this->cnvMessageFlagTypes;
        $flagTypes = $db->GetCol('SELECT cnvMessageFlagTypeID FROM ConversationFlaggedMessages WHERE cnvMessageID=?',array($this->cnvMessageID));
        $flags = array();
        foreach ($flagTypes as $flagType) {
            $flags[] = FlagType::getByID($flagType);
        }
        $this->cnvMessageFlagTypes = $flags;

        return $flags;
    }
    public function getConversationMessageTotalRatingScore() {return $this->cnvMessageTotalRatingScore;}

    public function getPermissionResponseClassName()
    {
        return '\\Concrete\\Core\\Permission\\Response\\ConversationResponse';
    }

    public function getPermissionAssignmentClassName()
    {
        return '\\Concrete\\Core\\Permission\\Assignment\\ConversationAssignment';
    }

    public function getPermissionObjectKeyCategoryHandle()
    {
        return 'conversation';
    }

    public function getPermissionObjectIdentifier()
    {
        return $this->getConversationMessageID();
    }

    public function getConversationMessageAuthorObject()
    {
        return $this->cnvMessageAuthor;
    }

    public function conversationMessageHasActiveChildren()
    {
        $db = Loader::db();
        $children = $db->getCol('SELECT cnvMessageID as cnt FROM ConversationMessages WHERE cnvMessageParentID=?',array($this->cnvMessageID));
        foreach ($children as $childID) {
            $child = static::getByID($childID);
            if (($child->isConversationMessageApproved() && !$child->isConversationMessageDeleted()) || $child->conversationMessageHasActiveChildren()) {
                return true;
            }
        }

        return false;
    }

    public function setMessageDateCreated($cnvMessageDateCreated)
    {
        $this->cnvMessageDateCreated = $cnvMessageDateCreated;
        $db = Loader::db();
        $db->Execute('update ConversationMessages set cnvMessageDateCreated = ? where cnvMessageID = ?', array(
            $cnvMessageDateCreated, $this->getConversationMessageID()
        ));
    }

    public function setMessageBody($cnvMessageBody)
    {
        $this->cnvMessageBody = $cnvMessageBody;
        $db = Loader::db();
        $db->Execute('update ConversationMessages set cnvMessageBody = ? where cnvMessageID = ?', array(
                $cnvMessageBody, $this->getConversationMessageID()
        ));
    }

    public function conversationMessageHasChildren()
    {
        $db = Loader::db();
        $count = $db->getOne('SELECT COUNT(cnvMessageID) as cnt FROM ConversationMessages WHERE cnvMessageParentID=?',array($this->cnvMessageID));

        return ($count > 0);
    }
    public function approve()
    {
        $db = Loader::db();
        $db->execute('UPDATE ConversationMessages SET cnvIsMessageApproved=1 WHERE cnvMessageID=?',array($this->cnvMessageID));
        $this->cnvIsMessageApproved = true;

        $cnv = $this->getConversationObject();
        if (is_object($cnv)) {
            $cnv->updateConversationSummary();
        }

    }
    public function unapprove()
    {
        $db = Loader::db();
        $db->execute('UPDATE ConversationMessages SET cnvIsMessageApproved=0 WHERE cnvMessageID=?',array($this->cnvMessageID));
        $this->cnvIsMessageApproved = false;

        $cnv = $this->getConversationObject();
        if (is_object($cnv)) {
            $cnv->updateConversationSummary();
        }
    }

    public function conversationMessageHasFlag($flag)
    {
        if (!$flag instanceof FlagType) {
            $flag = FlagType::getByHandle($flag);
        }
        if ($flag instanceof FlagType) {
            foreach ($this->getConversationMessageFlagTypes() as $type) {
                if ($flag->getConversationFlagTypeID() == $type->getConversationFlagTypeID()) {
                    return true;
                }
            }
        }

        return false;
    }
    public function getConversationMessageBodyOutput($dashboardOverride = false)
    {
        /** @var \Concrete\Core\Conversation\Editor\Editor $editor */
        $editor = ConversationEditor::getActive();
        if ($dashboardOverride) {
            return $editor->formatConversationMessageBody($this->getConversationObject(),$this->cnvMessageBody);
        } elseif ($this->cnvIsMessageDeleted) {
            return $editor->formatConversationMessageBody($this->getConversationObject(),t('This message has been deleted.'));
            //return t('This message has been deleted.');
        } elseif (!$this->cnvIsMessageApproved) {
            if ($this->conversationMessageHasFlag('spam')) {
                return $editor->formatConversationMessageBody($this->getConversationObject(),t('This message has been flagged as spam.'));
                //return t('This message has been flagged as spam.');
            }

            return $editor->formatConversationMessageBody($this->getConversationObject(),t('This message is queued for approval.'));
            // return t('This message is queued for approval.');
        } else {
            return $editor->formatConversationMessageBody($this->getConversationObject(),$this->cnvMessageBody);
        }
    }

    public function getConversationObject()
    {
        return Conversation::getByID($this->cnvID);
    }
    public function getConversationMessageUserObject()
    {
        return UserInfo::getByID($this->uID);
    }
    public function getConversationMessageUserID()
    {
        return $this->uID;
    }
    public function getConversationMessageDateTime()
    {
        return $this->cnvMessageDateCreated;
    }
    public function getConversationMessageDateTimeOutput($format = 'default')
    {
        $dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
        if (is_array($format)) { // custom date format

            return tc('Message posted date', 'Posted on %s', $dh->formatCustom($format[0], $this->cnvMessageDateCreated));
        }
        switch ($format) {
            case 'elapsed': // 3 seconds ago, 4 days ago, etc.
                $timestamp = strtotime($this->cnvMessageDateCreated);
                $time = array(
                    12 * 30 * 24 * 60 * 60 => 'Y',
                    30 * 24 * 60 * 60 => 'M',
                    24 * 60 * 60 => 'D',
                    60 * 60 => 'h',
                    60 => 'm',
                    1 => 's'
                );
                $ptime = time() - $timestamp;
                foreach ($time as $seconds => $unit) {
                    $elp = $ptime / $seconds;
                    if ($elp <= 0) {
                        return t2('%d second ago', '%d seconds ago', 0);
                    }
                    if ($elp >= 1) {
                        $rounded = round($elp);
                        switch ($unit) {
                            case 'Y':
                                return t2('%d year ago', '%d years ago', $rounded);
                            case 'M':
                                return t2('%d month ago', '%d months ago', $rounded);
                            case 'D':
                                return t2('%d day ago', '%d days ago', $rounded);
                            case 'h':
                                return t2('%d hour ago', '%d hours ago', $rounded);
                            case 'm':
                                return t2('%d minute ago', '%d minutes ago', $rounded);
                            case 's':
                                return t2('%d second ago', '%d seconds ago', $rounded);
                        }
                    }
                }
                break;
            case 'mdy':
                return tc('Message posted date', 'Posted on %s', $dh->formatDate($this->cnvMessageDateCreated));
            case 'mdy_full':
                return tc('Message posted date', 'Posted on %s', $dh->formatDate($this->cnvMessageDateCreated, true));
            case 'mdy_t':
                return tc('Message posted date', 'Posted on %s', $dh->formatDateTime($this->cnvMessageDateCreated));
            case 'mdy_full_t':
                return tc('Message posted date', 'Posted on %s', $dh->formatDateTime($this->cnvMessageDateCreated, true));
            case 'mdy_ts':
                return tc('Message posted date', 'Posted on %s', $dh->formatDateTime($this->cnvMessageDateCreated, false, true));
            case 'mdy_full_ts':
                return tc('Message posted date', 'Posted on %s', $dh->formatDateTime($this->cnvMessageDateCreated, true, true));
            default:
                return tc('Message posted date', 'Posted on %s', $dh->formatDate($this->cnvMessageDateCreated, true));
                break;
        }
    }
    public function rateMessage(Type $ratingType, $commentRatingIP, $commentRatingUserID, $post = array())
    {
        $db = Loader::db();
        if (!$this->hasRatedMessage($ratingType, $commentRatingUserID)) {
            $cnvRatingTypeID = $db->GetOne('SELECT * FROM ConversationRatingTypes WHERE cnvRatingTypeHandle = ?', array($ratingType->cnvRatingTypeHandle));
            $db->Execute('INSERT INTO ConversationMessageRatings (cnvMessageID, cnvRatingTypeID, cnvMessageRatingIP, timestamp, uID) VALUES (?, ?, ?, ?, ?)', array($this->getConversationMessageID(), $cnvRatingTypeID, $commentRatingIP, date('Y-m-d H:i:s'), $commentRatingUserID));
            $ratingType->adjustConversationMessageRatingTotalScore($this);
        }
    }

    public function hasRatedMessage(Type $ratingType, $user)
    {
        if (is_object($user)) {
            $uID = $user->getUserID();
        } else {
            $uID = $user;
        }

        $db = Loader::db();
        $cnt = $db->GetOne('select count(cnvMessageID) from ConversationMessageRatings where uID = ? and cnvRatingTypeID = ? and cnvMessageID = ?', array(
            $uID, $ratingType->getRatingTypeID(), $this->getConversationMessageID()
        ));
        return $cnt > 0;
    }

    public function getConversationMessageRating(Type $ratingType)
    {
        $db = Loader::db();
        $cnt = $db->GetOne('SELECT count(*) from ConversationMessageRatings where cnvRatingTypeID = ? AND cnvMessageID = ?',  array($ratingType->getConversationRatingTypeID(), $this->cnvMessageID));

        return $cnt;
    }

    public function flag($flagtype)
    {
        if ($flagtype instanceof FlagType) {
            $db = Loader::db();
            foreach ($this->getConversationMessageFlagTypes() as $ft) {
                if ($ft->getConversationFlagTypeID() === $flagtype->getConversationFlagTypeID()) {
                    return;
                }
            }
            $db->execute('INSERT INTO ConversationFlaggedMessages (cnvMessageFlagTypeID, cnvMessageID) VALUES (?,?)',array($flagtype->getConversationFlagTypeID(),$this->getConversationMessageID()));
            $this->cnvMessageFlagTypes[] = $flagtype;
            $this->unapprove();

            return true;
        }
        throw new \Exception(t('Invalid flag type.'));
    }

    public function unflag($flagtype)
    {
        if ($flagtype instanceof FlagType) {
            $db = Loader::db();
            $db->execute('DELETE FROM ConversationFlaggedMessages WHERE cnvMessageFlagTypeID = ? AND cnvMessageID = ?',array($flagtype->getConversationFlagTypeID(),$this->getConversationMessageID()));
            $this->cnvMessageFlagTypes[] = $flagtype;

            return true;
        }
        throw new \Exception(t('Invalid flag type.'));
    }

    public static function getByID($cnvMessageID)
    {
        $db = Loader::db();
        $r = $db->GetRow('select * from ConversationMessages where cnvMessageID = ?', array($cnvMessageID));
        if (is_array($r) && $r['cnvMessageID'] && $r['cnvMessageID'] == $cnvMessageID) {
            $msg = new static();
            $msg->getConversationMessageFlagTypes();
            $msg->setPropertiesFromArray($r);

            $author = new Author();
            $authorUser = ($r['uID'] > 0) ? \UserInfo::getByID($r['uID']) : null;
            if ($authorUser !== null) {
                $author->setUser($authorUser);
            } else {
                $author->setName($r['cnvMessageAuthorName']);
                $author->setEmail($r['cnvMessageAuthorEmail']);
                $author->setWebsite($r['cnvMessageAuthorWebsite']);
            }
            $msg->cnvMessageAuthor = $author;
            return $msg;
        }
    }

    public function attachFile(File $f)
    {
        $db = Loader::db();
        if (!is_object($f)) {
            return false;
        } else {
            $db->Execute('INSERT INTO ConversationMessageAttachments (cnvMessageID, fID) VALUES (?, ?)', array(
                $this->getConversationMessageID(),
                $f->getFileID()
            ));
            $fs = FileSet::createAndGetSet(Config::get('conversations.attachments_file_set'), FileSet::TYPE_PUBLIC, USER_SUPER_ID);
            $fsToRemove = FileSet::createAndGetSet(Config::get('conversations.attachments_pending_file_set'), FileSet::TYPE_PUBLIC, USER_SUPER_ID);
            $fs->addFileToSet($f);
            $fsToRemove->removeFileFromSet($f);
        }
        // associate with non-pending file set.
    }

    public function removeFile($cnvMessageAttachmentID)
    {
        $db = Loader::db();
        $db->Execute('DELETE FROM ConversationMessageAttachments WHERE cnvMessageAttachmentID = ?', array(
            $cnvMessageAttachmentID
        ));
        // remove from file manager.
    }

    public function getAttachments($cnvMessageID)
    {
        $db = Loader::db();
        $attachments = $db->GetAll('SELECT * FROM ConversationMessageAttachments WHERE cnvMessageID = ?', array(
            $cnvMessageID
        ));

        return $attachments;
    }

    public static function getByAttachmentID($cnvMessageAttachmentID)
    {
        $db = Loader::db();
        $messageID = $db->GetOne('SELECT cnvMessageID FROM ConversationMessageAttachments WHERE cnvMessageAttachmentID = ?', array(
        $cnvMessageAttachmentID
        ));

        if ($messageID) {
            $message = self::getByID($messageID);
            return $message;
        }
    }

    public static function add(\Concrete\Core\Conversation\Conversation $cnv, Author $author, $cnvMessageSubject, $cnvMessageBody, $parentMessage = false)
    {
        $db = Loader::db();
        $date = Loader::helper('date')->getOverridableNow();

        $uID = 0;
        $user = $author->getUser();
        $cnvMessageAuthorName = $author->getName();
        $cnvMessageAuthorEmail = $author->getEmail();
        $cnvMessageAuthorWebsite = $author->getWebsite();

        if (is_object($user)) {
            $uID = $user->getUserID();
        }

        $cnvMessageParentID = 0;
        $cnvMessageLevel = 0;
        if (is_object($parentMessage)) {
            $cnvMessageParentID = $parentMessage->getConversationMessageID();
            $cnvMessageLevel = $parentMessage->getConversationMessageLevel() + 1;
        }

        $cnvID = 0;
        if ($cnv instanceof Conversation) {
            $cnvID = $cnv->getConversationID();
        }

        $editor = ConversationEditor::getActive();
        $cnvEditorID = $editor->getConversationEditorID();

        /** @var \Concrete\Core\Permission\IPService $iph */
        $iph = Core::make('helper/validation/ip');
        $ip = $iph->getRequestIP();
        $r = $db->Execute('insert into ConversationMessages (cnvMessageSubject, cnvMessageBody, cnvMessageDateCreated, cnvMessageParentID, cnvEditorID, cnvMessageLevel, cnvID, uID, cnvMessageAuthorName, cnvMessageAuthorEmail, cnvMessageAuthorWebsite, cnvMessageSubmitIP, cnvMessageSubmitUserAgent) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                          array($cnvMessageSubject, $cnvMessageBody, $date, $cnvMessageParentID, $cnvEditorID, $cnvMessageLevel, $cnvID, $uID, $cnvMessageAuthorName, $cnvMessageAuthorEmail, $cnvMessageAuthorWebsite, ($ip === false)?(''):($ip->getIp()), $_SERVER['HTTP_USER_AGENT']));

        $cnvMessageID = $db->Insert_ID();
        
        $message = static::getByID($cnvMessageID);

        $event = new MessageEvent($message);
        Events::dispatch('on_new_conversation_message', $event);

        if ($cnv instanceof \Concrete\Core\Conversation\Conversation) {
            $cnv->updateConversationSummary();
            $users = $cnv->getConversationUsersToEmail();
            $c = $cnv->getConversationPageObject();
            if (is_object($c)) {
                $formatter = new AuthorFormatter($author);
                $cnvMessageBody = html_entity_decode($cnvMessageBody, ENT_QUOTES, APP_CHARSET);
                foreach($users as $ui) {
                    $mail = Core::make('mail');
                    $mail->to($ui->getUserEmail());
                    $mail->addParameter('title', $c->getCollectionName());
                    $mail->addParameter('link', $c->getCollectionLink(true));
                    $mail->addParameter('poster', $formatter->getDisplayName());
                    $mail->addParameter('body', Core::make('helper/text')->prettyStripTags($cnvMessageBody));
                    $mail->load('new_conversation_message');
                    $mail->sendMail();
                }
            }
        }

        return static::getByID($cnvMessageID);
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute('update ConversationMessages set cnvIsMessageDeleted = 1, cnvIsMessageApproved = 0 where cnvMessageID = ?', array(
            $this->cnvMessageID
        ));

        $cnv = $this->getConversationObject();
        if (is_object($cnv)) {
            $cnv->updateConversationSummary();
        }

        $this->cnvIsMessageDeleted = true;
    }

    public function restore()
    {
        $db = Loader::db();
        $db->Execute('update ConversationMessages set cnvIsMessageDeleted = 0 where cnvMessageID = ?', array(
            $this->cnvMessageID
        ));

        $cnv = $this->getConversationObject();
        if (is_object($cnv)) {
            $cnv->updateConversationSummary();
        }

        $this->cnvIsMessageDeleted = false;
    }

}
