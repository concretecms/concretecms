<?php

namespace Concrete\Block\Discussion;

use Concrete\Core\Block\BlockController;
use Core;
use Database;

class Controller extends BlockController
{
    protected $btCacheBlockRecord = true;
    protected $btTable = 'btDiscussion';

    public function getBlockTypeDescription()
    {
        return t("Places a discussion a page.");
    }

    public function getBlockTypeName()
    {
        return t("Discussion");
    }

    public function getConversationDiscussionObject()
    {
        if (!isset($this->discussion)) {
            $db = Database::connection();
            $cnvDiscussionID = $db->GetOne('select cnvDiscussionID from btDiscussion where bID = ?', array($this->bID));
            $this->discussion = ConversationDiscussion::getByID($cnvDiscussionID);
        }

        return $this->discussion;
    }

    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('core/conversation');
        $this->requireAsset('css', 'core/frontend/pagination');
    }

    public function view()
    {
        $discussion = $this->getConversationDiscussionObject();
        if (is_object($discussion)) {
            $this->set('discussion', $discussion);
            if ($this->enableNewTopics && $this->ptID) {
                $pt = PageType::getByID($this->ptID);
                $this->set('pagetype', $pt);
                Core::make('helper/concrete/composer')->addAssetsToRequest($pt, $this);
            }

            $c = Page::getCurrentPage();
            $dl = new ConversationDiscussionList($c);
            $orderBy = $this->orderBy;
            if (in_array($_REQUEST['orderBy'], array('replies', 'date', 'date_last_message')) && $this->enableOrdering) {
                $orderBy = $_REQUEST['orderBy'];
            }
            switch ($orderBy) {
                case 'replies':
                    $dl->sortByTotalReplies();
                    break;
                case 'date':
                    $dl->sortByPublicDateDescending();
                    break;
                default: //date_last_message
                    $dl->sortByConversationDateLastMessage();
                    break;
            }
            if ($this->itemsPerPage > 0) {
                $dl->setItemsPerPage($this->itemsPerPage);
            }
            $pages = $dl->getPage();
            $this->set('reqOrderBy', $orderBy);
            $this->set('topics', $pages);
            $this->set('list', $dl);
        }
    }

    public function action_post()
    {
        // happens through ajax
        $pagetype = PageType::getByID($this->ptID);
        if (is_object($pagetype) && $this->enableNewTopics) {
            $ccp = new Permissions($pagetype);
            if ($ccp->canAddPageType()) {
                $pagetypes = $pagetype->getPageTypeComposerPageTypeObjects();
                $ctTopic = $pagetypes[0];
                $c = Page::getCurrentPage();
                $e = $pagetype->validatePublishRequest($ctTopic, $c);
                $r = new PageTypePublishResponse($e);
                if (!$e->has()) {
                    $d = $pagetype->createDraft($ctTopic);
                    $d->setPageDraftTargetParentPageID($c->getCollectionID());
                    $d->saveForm();
                    $d->publish();
                    $nc = Page::getByID($d->getCollectionID(), 'RECENT');
                    $link = Core::make('helper/navigation')->getLinkToCollection($nc, true);
                    $r->setRedirectURL($link);
                }
                $r->outputJSON();
            }
        }
        exit;
    }

    public function save($post)
    {
        $db = Database::connection();
        $cnvID = $db->GetOne('select cnvDiscussionID from btDiscussion where bID = ?', array($this->bID));
        if (!$cnvID) {
            $c = Page::getCurrentPage();
            $discussion = ConversationDiscussion::add($c);
        } else {
            $discussion = ConversationDiscussion::getByID($cnvID);
        }
        $values = $post;
        $ptID = 0;
        if ($post['ptID']) {
            $ptID = $post['ptID'];
        }
        $values['ptID'] = $ptID;
        $values['cnvDiscussionID'] = $discussion->getConversationDiscussionID();
        parent::save($values);
    }
}
