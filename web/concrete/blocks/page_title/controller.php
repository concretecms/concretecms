<?php

namespace Concrete\Block\PageTitle;

use Page;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Tree\Node\Type\Topic;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends BlockController
{
    public $helpers = array('form');

    protected $btInterfaceWidth = 400;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = false;
    protected $btInterfaceHeight = 400;
    protected $btTable = 'btPageTitle';
    protected $btWrapperClass = 'ccm-ui';

    public function getBlockTypeDescription()
    {
        return t("Displays a Page's Title");
    }

    public function getBlockTypeName()
    {
        return t("Page Title");
    }

    public function getSearchableContent()
    {
        return $this->getTitleText();
    }

    public function getTitleText()
    {
        if ($this->useCustomTitle && strlen($this->titleText)) {
            $title = $this->titleText;
        } else {
            $p = Page::getCurrentPage();
            if ($p instanceof Page) {
                $title = $p->getCollectionName();
                if (!strlen($title) && $p->isMasterCollection()) {
                    $title = '[' . t('Page Title') . ']';
                }
            } else {
                $title = '';
            }
        }

        return $title;
    }

    public function view()
    {
        if (!(isset($this->formatting) && $this->formatting)) {
            $this->set('formatting', 'h1');
        }
        $this->set('title', $this->getTitleText());
    }

    public function save($data)
    {
        $data['useCustomTitle'] = ($data['useCustomTitle'] ? 1 : 0);
        parent::save($data);
    }

    public function action_topic($treeNodeID = false, $topic = false)
    {
        if ($treeNodeID) {
            $topicObj = Topic::getByID(intval($treeNodeID));
            if ($topicObj instanceof Topic) {
                $this->set('currentTopic', $topicObj);
            }
        }
        $this->view();
    }
}
