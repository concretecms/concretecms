<?php

namespace Concrete\Block\TopicList;
defined('C5_EXECUTE') or die("Access Denied.");
use Concrete\Core\Block\BlockController;
use Concrete\Core\Tree\Tree;
use Concrete\Core\Tree\Type\Topic as TopicTree;
use Concrete\Core\Tree\Type\Topic;
use Core;
use Loader;

class Controller extends BlockController
{

    public $helpers = array('form');

    protected $btInterfaceWidth = 400;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btInterfaceHeight = 250;
    protected $btTable = 'btTopicList';

    public function getBlockTypeDescription()
    {
        return t("Displays a list of your site's topics, allowing you to click on them to filter a page list.");
    }

    public function getBlockTypeName()
    {
        return t("Topic List");
    }

    public function add()
    {
        $this->edit();
    }

    public function edit()
    {
        $this->requireAsset('core/topics');
        $tt = new TopicTree();
        $defaultTree = $tt->getDefault();
        $tree = $tt->getByID(Loader::helper('security')->sanitizeInt($this->topicTreeID));
        if (!$tree) {
            $tree = $defaultTree;
        }
        $trees = $tt->getList();
        $this->set('tree', $tree);
        $this->set('trees', $trees);
    }

    public function view()
    {
        $tt = new TopicTree();
        $tree = $tt->getByID(Loader::helper('security')->sanitizeInt($this->topicTreeID));
        $this->set('tree', $tree);
    }

    public function action_topic($topic = false) {
        $this->set('selectedTopicID', intval($topic));
        $this->view();
    }

    public function getTopicLink(\Concrete\Core\Tree\Node\Type\Topic $topic)
    {
        if ($this->cParentID) {
            $c = \Page::getByID($this->cParentID);
        } else {
            $c = \Page::getCurrentPage();
        }

        return \URL::page($c, 'topic', $topic->getTreeNodeID());
    }

    public static function replaceTreeWithPlaceHolder($treeID) {
        if ($treeID > 0) {
            $tree = Tree::getByID($treeID);
            if (is_object($tree)) {
                return '{ccm:export:tree:' . $tree->getTreeDisplayName() . '}';
            }
        }
    }

    public function export(\SimpleXMLElement $blockNode) {
        $tree = Tree::getByID($this->topicTreeID);
        if (is_object($tree)) {
            $data = $blockNode->addChild('data');
            $data->addChild('tree', $tree->getTreeDisplayName());
            $path = null;
            if ($this->cParentID) {
                $parent = \Page::getByID($this->cParentID);
                $path = '{ccm:export:page:' . $parent->getCollectionPath() . '}';
            }
            $data->addChild('cParentID', $path);
        }
    }

    public function getImportData($blockNode, $page) {
        $args = array();
        $treeName = (string) $blockNode->data->tree;
        $page = (string) $blockNode->data->cParentID;
        $tree = Topic::getByDisplayName($treeName);
        $args['topicTreeID'] = $tree->getTreeID();
        $args['cParentID'] = 0;
        if ($page) {
            if (preg_match('/\{ccm:export:page:(.*)\}/i', $page, $matches)) {
                $c = \Page::getByPath($matches[1]);
                $args['externalTarget'] = 1;
                $args['cParentID'] = $c->getCollectionID();
            }
        }
        return $args;
    }

    public function save($data)
    {
        $externalTarget = intval($data['externalTarget']);
        if ($externalTarget === 0) {
            $data['cParentID'] = 0;
        } else {
            $data['cParentID'] = intval($data['cParentID']);
        }

        parent::save($data);
    }
}