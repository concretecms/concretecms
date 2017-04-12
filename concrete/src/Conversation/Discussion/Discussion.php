<?php
namespace Concrete\Core\Conversation\Discussion;

use Loader;
use Core;
use Concrete\Core\Foundation\Object;
use Page;

class Discussion extends Object
{
    public static function add(Page $c)
    {
        $db = Loader::db();
        $cID = $c->getCollectionID();
        $date = Loader::helper('date')->getOverridableNow();
        $r = $db->Execute('insert into ConversationDiscussions (cnvDiscussionDateCreated, cID) values (?, ?)', array($date, $cID));

        return static::getByID($db->Insert_ID());
    }

    public function getConversationDiscussionCollectionObject()
    {
        $c = Page::getByID($this->cID);
        if (is_object($c) && !$c->isError()) {
            return $c;
        }
    }

    public function getConversationDiscussionID()
    {
        return $this->cnvDiscussionID;
    }
    public function getConversationDiscussionPageTypeID()
    {
        return $this->ptID;
    }
    public function getConversationDiscussionCollectionID()
    {
        return $this->cID;
    }

    public function getConversationDiscussionDateTime()
    {
        return $this->cnvDiscussionDateCreated;
    }
    public function getConversationDiscussionDateTimeOutput()
    {
        return tc('Message posted date', 'Posted on %s', Core::make('date')->formatDateTime($this->cnvDiscussionDateCreated, true));
    }

    public static function getByID($cnvDiscussionID)
    {
        $db = Loader::db();
        $r = $db->GetRow('select * from ConversationDiscussions where cnvDiscussionID = ?', array($cnvDiscussionID));
        if (is_array($r) && $r['cnvDiscussionID'] == $cnvDiscussionID) {
            $d = new static();
            $d->setPropertiesFromArray($r);

            return $d;
        }
    }
}
