<?php
namespace Concrete\Attribute\Topics;

use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Key\Type\TopicsType;
use Concrete\Core\Entity\Attribute\Value\Value\SelectedTopic;
use Concrete\Core\Entity\Attribute\Value\Value\TopicsValue;
use Concrete\Core\Search\ItemList\Database\AttributedItemList;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Type\Topic as TopicTree;
use Concrete\Core\Tree\Tree;
use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Attribute\Controller as AttributeTypeController;
use Core;
use Database;

class Controller extends AttributeTypeController
{
    protected $searchIndexFieldDefinition = array(
        'type' => 'text',
        'options' => array('length' => 4294967295, 'default' => null, 'notnull' => false),
    );

    public $helpers = array('form');

    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('tag');
    }

    public function filterByAttribute(AttributedItemList $list, $value, $comparison = '=')
    {
        if ($value instanceof TreeNode) {
            $topic = $value;
        } else {
            $topic = Node::getByID(intval($value));
        }
        if (is_object($topic) && $topic instanceof \Concrete\Core\Tree\Node\Type\Topic) {
            $column = 'ak_' . $this->attributeKey->getAttributeKeyHandle();
            $qb = $list->getQueryObject();
            $qb->andWhere(
                $qb->expr()->like($column, ':topicPath')
            );
            $qb->setParameter('topicPath', "%||" . $topic->getTreeNodeDisplayPath() . '%||');
        }
    }

    public function saveKey($data)
    {
        $type = $this->getAttributeKeyType();
        $data += array(
            'akTopicParentNodeID' => null,
            'akTopicTreeID' => null,
        );
        $akTopicParentNodeID = $data['akTopicParentNodeID'];
        $akTopicTreeID = $data['akTopicTreeID'];
        $type->setParentNodeID($akTopicParentNodeID);
        $type->setTopicTreeID($akTopicTreeID);

        return $type;
    }

    public function getDisplayValue()
    {
        $list = $this->attributeValue->getValue()->getSelectedTopics();
        $topics = array();
        foreach ($list as $node) {
            $topic = Node::getByID($node->getTreeNodeID());
            if (is_object($topic)) {
                $topics[] = $topic->getTreeNodeDisplayName();
            }
        }

        return implode(', ', $topics);
    }

    public function getDisplaySanitizedValue()
    {
        return $this->getDisplayValue();
    }

    /**
     * @deprecated
     */
    public function getSelectedOptions()
    {
        return $this->attributeValue->getValue()->getSelectedTopics();
    }

    public function exportValue(\SimpleXMLElement $akn)
    {
        $avn = $akn->addChild('topics');
        $nodes = $this->attributeValue->getValue()->getSelectedTopics();
        foreach ($nodes as $node) {
            $topic = Node::getByID($node->getTreeNodeID());
            if (is_object($topic)) {
                $avn->addChild('topic', $topic->getTreeNodeDisplayPath());
            }
        }
    }

    public function importValue(\SimpleXMLElement $akn)
    {
        $selected = array();
        if (isset($akn->topics)) {
            foreach ($akn->topics->topic as $topicPath) {
                $selected[] = (string) $topicPath;
            }

            return $this->saveValue($selected);
        }
    }

    public function saveValue($nodes)
    {
        $selected = array();
        $this->load();
        $tree = Tree::getByID($this->akTopicTreeID);
        foreach ($nodes as $topicPath) {
            $node = $tree->getNodeByDisplayPath($topicPath);
            if (is_object($node)) {
                $selected[] = $node->getTreeNodeID();
            }
        }

        $topicsValue = new TopicsValue();
        foreach ($selected as $treeNodeID) {
            $topicsValueNode = new SelectedTopic();
            $topicsValueNode->setAttributeValue($topicsValue);
            $topicsValueNode->setTreeNodeID($treeNodeID);
            $topicsValue->getSelectedTopics()->add($topicsValueNode);
        }

        return $topicsValue;
    }

    public function exportKey($key)
    {
        $this->load();
        $tree = Tree::getByID($this->akTopicTreeID);
        $node = Node::getByID($this->akTopicParentNodeID);
        $path = $node->getTreeNodeDisplayPath();
        $treeNode = $key->addChild('tree');
        $treeNode->addAttribute('name', $tree->getTreeName());
        $treeNode->addAttribute('path', $path);

        return $key;
    }

    public function importKey($key)
    {
        $type = new TopicsType();
        $name = (string) $key->tree['name'];
        $tree = \Concrete\Core\Tree\Type\Topic::getByName($name);
        $node = $tree->getNodeByDisplayPath((string) $key->tree['path']);
        $type->setTopicTreeID($tree->getTreeID());
        $type->setParentNodeID($node->getTreeNodeID());

        return $type;
    }

    public function form($additionalClass = false)
    {
        $this->load();
        $this->requireAsset('core/topics');
        $this->requireAsset('javascript', 'jquery/form');
        if (is_object($this->attributeValue)) {
            $value = $this->getAttributeValue()->getValue();
        }
        if (is_object($this->attributeValue)) {
            $valueIDs = array();
            foreach ($this->attributeValue->getValue()->getSelectedTopics() as $value) {
                $valueID = $value->getTreeNodeID();
                $withinParentScope = false;
                $nodeObj = TreeNode::getByID($value->getTreeNodeID());
                if (is_object($nodeObj)) {
                    $parentNodeArray = $nodeObj->getTreeNodeParentArray();
                    // check to see if selected node is still within parent scope, in case it has been changed.
                    foreach ($parentNodeArray as $parent) {
                        if ($parent->treeNodeID == $this->akTopicParentNodeID) {
                            $withinParentScope = true;
                            break;
                        }
                    }
                    if ($withinParentScope) {
                        $valueIDs[] = $valueID;
                    }
                }
            }
            $this->set('valueIDs', implode(',', $valueIDs));
        }
        $this->set('valueIDArray', $valueIDs);
        $ak = $this->getAttributeKey();
        $this->set('akID', $ak->getAttributeKeyID());
        $this->set('parentNode', $this->akTopicParentNodeID);
        $this->set('treeID', $this->akTopicTreeID);
    }

    public function searchForm($list)
    {
        $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), $this->request('treeNodeID'));
        return $list;
    }

    public function getSearchIndexValue()
    {
        $str = "||";
        $nodeKeys = $this->attributeValue->getValue()->getSelectedTopics();
        foreach ($nodeKeys as $nodeKey) {
            $nodeObj = TreeNode::getByID($nodeKey->getTreeNodeID());
            if (is_object($nodeObj)) {
                $str .= $nodeObj->getTreeNodeDisplayPath() . "||";
            }
        }
        // remove line break for empty list
        if ($str == "\n") {
            return '';
        }

        return $str;
    }

    public function search()
    {
        $this->requireAsset('core/topics');
        $this->load();
        $tree = TopicTree::getByID(Core::make('helper/security')->sanitizeInt($this->akTopicTreeID));
        $this->set('tree', $tree);
        $treeNodeID = $this->request('treeNodeID');
        if (!$treeNodeID) {
            $treeNodeID = $this->akTopicParentNodeID;
        }
        $this->set('selectedNode', $treeNodeID);
        $this->set('attributeKey', $this->attributeKey);
    }

    public function saveForm()
    {
        $sh = Core::make('helper/security');
        $av = new TopicsValue();
        $cleanIDs = array();
        $topicsArray = $_POST['topics_' . $this->attributeKey->getAttributeKeyID()];
        if (is_array($topicsArray) && count($topicsArray) > 0) {
            foreach ($topicsArray as $topicID) {
                $cleanIDs[] = $sh->sanitizeInt($topicID);
            }
            foreach ($cleanIDs as $topID) {
                $topic = new SelectedTopic();
                $topic->setAttributeValue($av);
                $topic->setTreeNodeID($topID);
                $av->getSelectedTopics()->add($topic);
            }
        }

        return $av;
    }

    public function type_form()
    {
        $this->requireAsset('core/topics');
        $this->requireAsset('javascript', 'jquery/form');
        $this->load();
        $tt = new TopicTree();
        $defaultTree = $tt->getDefault();
        $topicTreeList = $tt->getList();
        $tree = $tt->getByID(Core::make('helper/security')->sanitizeInt($this->akTopicTreeID));
        if (!$tree) {
            $tree = $defaultTree;
        }
        $this->set('tree', $tree);
        $trees = array();
        if (is_object($defaultTree)) {
            $trees[] = $defaultTree;
            foreach ($topicTreeList as $ctree) {
                if ($ctree->getTreeID() != $defaultTree->getTreeID()) {
                    $trees[] = $ctree;
                }
            }
        }
        $this->set('trees', $trees);
        $this->set('parentNode', $this->akTopicParentNodeID);
    }

    public function validateKey($data = false)
    {
        if ($data == false) {
            $data = $this->post();
        }
        $e = parent::validateKey($data);
        if (!$data['akTopicParentNodeID'] || !$data['akTopicTreeID']) {
            $e->add(t('You must specify a valid topic tree parent node ID and topic tree ID.'));
        }

        return $e;
    }

    public function validateValue()
    {
        $val = $this->getValue();

        return is_array($val) && count($val) > 0;
    }

    public function validateForm($p)
    {
        $topicsArray = $_POST['topics_' . $this->attributeKey->getAttributeKeyID()];

        return is_array($topicsArray) && count($topicsArray) > 0;
    }

    public function getTopicParentNode()
    {
        $this->load();

        return $this->akTopicParentNodeID;
    }

    public function getTopicTreeID()
    {
        $this->load();

        return $this->akTopicTreeID;
    }

    protected function load()
    {
        $ak = $this->getAttributeKey();
        if (!is_object($ak)) {
            var_dump_safe($this);

            return false;
        }
        $this->akTopicParentNodeID = $ak->getAttributeKeyType()->getParentNodeID();
        $this->akTopicTreeID = $ak->getAttributeKeyType()->getTopicTreeID();
    }

    public function duplicateKey($newAK)
    { // TODO this is going to need some work to function with the child options table...
        $this->load();
        $db = Database::get();
        $db->Replace(
            'atTopicSettings',
            array(
                'akID' => $newAK->getAttributeKeyID(),
                'akTopicParentNodeID' => $this->akTopicParentNodeID,
                'akTopicTreeID' => $this->akTopicTreeID,
            ),
            array('akID'),
            true
        );
    }

    public function createAttributeKeyType()
    {
        return new TopicsType();
    }
}
