<?
namespace Concrete\Attribute\Topics;
use Concrete\Core\Tree\Node\Node;
use Loader;
use \Concrete\Core\Foundation\Object;
use \Concrete\Core\Tree\Type\Topic as TopicTree;
use \Concrete\Core\Tree\Tree;
use \Concrete\Core\Tree\Node\Node as TreeNode;
use \Concrete\Core\Attribute\Controller as AttributeTypeController;
class Controller extends AttributeTypeController  {

	protected $searchIndexFieldDefinition = array('type' => 'text', 'options' => array('length' => 4294967295, 'default' => null, 'notnull' => false));

	public $helpers = array('form');
	
	public function saveKey($data) {
		$akTopicParentNodeID = $data['akTopicParentNodeID'];
		$akTopicTreeID = $data['akTopicTreeID'];
		$this->setNodes($akTopicParentNodeID, $akTopicTreeID);
		// trap dynatree display node / tree node here. 
	}

	public function getDisplaySanitizedValue() {
		//$this->load();
		//return parent::getDisplaySanitizedValue();
	}

	public static function getSelectedOptions($avID) {
		//$avID = $this->getAttributeValueID();
		$db = Loader::db();
		$optionIDs = $db->execute(
			'select TopicNodeID from atSelectedTopics where avID=?',
			array($avID)
		);
		return $optionIDs;
	}

    public function exportKey($key) {
        $this->load();
        $tree = Tree::getByID($this->akTopicTreeID);
        $node = Node::getByID($this->akTopicParentNodeID);
        $path = '/';
        $nodes = $node->getTreeNodeParentArray();
        foreach($nodes as $n) {
            if ($n->getTreeNodeID() == $tree->getRootTreeNodeID()) {
                continue;
            }
            $path .= $n->getTreeNodeDisplayName() . '/';
        }
        if ($node->getTreeNodeID() != $tree->getRootTreeNodeID()) {
            $path .= $node->getTreeNodeDisplayName();
        }

        $treeNode = $key->addChild('tree');
        $treeNode->addAttribute('name', $tree->getTreeDisplayName());
        $treeNode->addAttribute('path', $path);
        return $akey;
    }

    public function form($additionalClass = false) {
		$this->load();
        $this->requireAsset('core/topics');
        $this->requireAsset('javascript', 'jquery/form');
		if (is_object($this->attributeValue)) {
			$value = $this->getAttributeValue()->getValue();
		}
		if($this->getAttributeValueID()) {
			$valueIDs = array(); 
			foreach($this->getSelectedOptions($this->getAttributeValueID()) as $valueID) {
				$withinParentScope = false;
				$nodeObj = TreeNode::getByID($valueID['TopicNodeID']);
				if(is_object($nodeObj)) {
					$parentNodeArray = $nodeObj->getTreeNodeParentArray();
					 // check to see if selected node is still within parent scope, in case it has been changed. 
					foreach($parentNodeArray as $parent) {
						if($parent->treeNodeID == $this->akTopicParentNodeID) {
							$withinParentScope = true;
							break;
						}
					}
					if($withinParentScope) {
						$valueIDs[] = $valueID['TopicNodeID'];
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
		$this->set('avID', $this->getAttributeValueID());
	}

	public function searchForm($list) {
		//$db = Loader::db();
		//$list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), '%' . $this->request('value') . '%', 'like');
		//return $list;
	}
	
	public function getSearchIndexValue() {
        $str = "\n";
        $nodeKeys = $this->getSelectedOptions($this->getAttributeValueID());
        foreach($nodeKeys as $nodeKey) {
           $nodeObj = TreeNode::getByID($nodeKey['TopicNodeID']);
           $nodeParentArray = array_reverse($nodeObj->getTreeNodeParentArray()); // order array from general to specific
			$parentNameArray = array();
			foreach($nodeParentArray as $nodeParent) {
			   $parentNameArray[] = $nodeParent->getTreeNodeDisplayName();
			}
			array_shift($parentNameArray);  // pop top level categories off the list
			$implodedList = implode('>', $parentNameArray);
			$implodedList .= '>'.$nodeObj->getTreeNodeDisplayName();
			$str .=  $implodedList . "\n";	
        }
        // remove line break for empty list
        if ($str == "\n") {
            return '';
        }
        return $str;
    }
	
	 public function search() {
		//$f = Loader::helper('form');
		//print $f->text($this->field('value'), $this->request('value'));
	}
	

	 public function setNodes($akTopicParentNodeID, $akTopicTreeID) {
		$db = Loader::db();
		$ak = $this->getAttributeKey();
		$db->Replace('atTopicSettings', array(
			'akID' => $ak->getAttributeKeyID(),
			'akTopicParentNodeID' => $akTopicParentNodeID,
			'akTopicTreeID' => $akTopicTreeID
		), array('akID'), true);
	} 
	
	
	public function saveForm() {
		$db = Loader::db();
		$this->saveValue($data);
		$sh = Loader::helper('security');
		$ak = $this->getAttributeKey();
		$cleanIDs = array();
		$topicsArray = $_POST['topics_'.$ak->getAttributeKeyID()];
		if(is_array($topicsArray) && count($topicsArray) > 0) {
			foreach($topicsArray as $topicID) {
				$cleanIDs[] = $sh->sanitizeInt($topicID);
			}
			foreach($cleanIDs as $topID) {
				$db->execute('INSERT INTO atSelectedTopics (avID, TopicNodeID) VALUES (?, ?)', array($this->getAttributeValueID(), $topID));
			}
		}
	}
	
	public function saveValue($data) {
		$data = $this->getAttributeValueID(); 
	}
	
	public function getValue() {
		$this->load();
		$this->set('parentNode', $this->akTopicParentNodeID);
		$this->set('treeID', $this->akTopicTreeID);
		$this->set('avID', $avID); 
		$ak = $this->getAttributeKey();
		$this->set('akID', $ak->getAttributeKeyID());
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atDefault where avID = ?', array($id));  
		}
		
		$db->Execute('delete from atTopicSettings where akID = ?', array($this->attributeKey->getAttributeKeyID()));
	}
	
	public function type_form() {
        $this->requireAsset('core/topics');
        $this->requireAsset('javascript', 'jquery/form');
		$this->load();
		$tt = new TopicTree();
		$defaultTree = $tt->getDefault();
		$topicTreeList = $tt->getList();
		$tree = $tt->getByID(Loader::helper('security')->sanitizeInt($this->akTopicTreeID));
		if (!$tree) {
			$tree = $defaultTree;
		}
		$this->set('tree', $tree);
		$trees = array();
		if (is_object($defaultTree)) {
			$trees[] = $defaultTree;
			foreach($topicTreeList as $ctree) {
				if ($ctree->getTreeID() != $defaultTree->getTreeID()) {
					$trees[] = $ctree;
				}
			}
		}
		$this->set('trees', $trees);
		$this->set('parentNode', $this->akTopicParentNodeID);
	}
	
	public function validateForm($data) {
		// TODO: form validation
	}
	
	public function getTopicParentNode() {
		$this->load();
		return $this->akTopicParentNodeID;
	}
	
	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
		$db = Loader::db();
		$row = $db->GetRow('select * from atTopicSettings where akID = ?', $ak->getAttributeKeyID());
		$this->akTopicParentNodeID = $row['akTopicParentNodeID'];
		$this->akTopicTreeID = $row['akTopicTreeID'];
	}
	
	public function duplicateKey($newAK) { // TODO this is going to need some work to function with the child options table... 
		$this->load();
		$db = Loader::db();
		$db->Replace('atTopicSettings', array(
			'akID' => $newAK->getAttributeKeyID(), 
			'akTopicParentNodeID' => $this->akTopicParentNodeID,
			'akTopicTreeID' => $this->akTopicTreeID
		), array('akID'), true);
	}
}