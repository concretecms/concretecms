<?php
namespace Concrete\Core\Page\Type\Composer;
use \Concrete\Core\Foundation\Object;
use Concrete\Core\Page\Type\Type;
use PageType;
use Loader;
use \Concrete\Core\Page\Type\Composer\Control\Type\Type as PageTypeComposerControlType;

class FormLayoutSet extends Object {

	public function getPageTypeComposerFormLayoutSetID() {return $this->ptComposerFormLayoutSetID;}
	public function getPageTypeComposerFormLayoutSetName() {return $this->ptComposerFormLayoutSetName;}
	public function getPageTypeComposerFormLayoutSetDescription() {return $this->ptComposerFormLayoutSetDescription;}
	public function getPageTypeComposerFormLayoutSetDisplayOrder() {return $this->ptComposerFormLayoutSetDisplayOrder;}
	public function getPageTypeID() {return $this->ptID;}

    /**
     * @return \Concrete\Core\Page\Type\Type
     */
    public function getPageTypeObject() {return PageType::getByID($this->ptID);}
	/** Returns the display name for this instance (localized and escaped accordingly to $format)
	* @param string $format = 'html' Escape the result in html format (if $format is 'html'). If $format is 'text' or any other value, the display name won't be escaped.
	* @return string
	*/
	public function getPageTypeComposerFormLayoutSetDisplayName($format = 'html') {
		$value = tc('PageTypeComposerFormLayoutSetName', $this->ptComposerFormLayoutSetName);
		switch ($format) {
			case 'html':
				return h($value);
			case 'text':
			default:
				return $value;
		}
	}
	public function getPageTypeComposerFormLayoutSetDisplayDescription($format = 'html') {
		$value = tc('PageTypeComposerFormLayoutSetDescription', $this->ptComposerFormLayoutSetDescription);
		switch ($format) {
			case 'html':
				return h($value);
			case 'text':
			default:
				return $value;
		}
	}
	public static function getList(Type $pagetype) {
		$db = Loader::db();
		$ptComposerFormLayoutSetIDs = $db->GetCol('select ptComposerFormLayoutSetID from PageTypeComposerFormLayoutSets where ptID = ? order by ptComposerFormLayoutSetDisplayOrder asc', array($pagetype->getPageTypeID()));
		$list = array();
		foreach($ptComposerFormLayoutSetIDs as $ptComposerFormLayoutSetID) {
			$set = static::getByID($ptComposerFormLayoutSetID);
			if (is_object($set)) {
				$list[] = $set;
			}
		}
		return $list;
	}

	public static function getByID($ptComposerFormLayoutSetID) {
		$db = Loader::db();
		$r = $db->GetRow('select * from PageTypeComposerFormLayoutSets where ptComposerFormLayoutSetID = ?', array($ptComposerFormLayoutSetID));
		if (is_array($r) && $r['ptComposerFormLayoutSetID']) {
			$set = new static;
			$set->setPropertiesFromArray($r);
			return $set;
		}
	}

	public function export($fxml) {
		$node = $fxml->addChild('set');
		$node->addAttribute('name', $this->getPageTypeComposerFormLayoutSetName());
		$node->addAttribute('description', $this->getPageTypeComposerFormLayoutSetDescription());
		$controls = FormLayoutSetControl::getList($this);
		foreach($controls as $con) {
			$con->export($node);
		}
	}

	public function updateFormLayoutSetName($ptComposerFormLayoutSetName) {
		$db = Loader::db();
		$db->Execute('update PageTypeComposerFormLayoutSets set ptComposerFormLayoutSetName = ? where ptComposerFormLayoutSetID = ?', array(
			$ptComposerFormLayoutSetName, $this->ptComposerFormLayoutSetID
		));
		$this->ptComposerFormLayoutSetName = $ptComposerFormLayoutSetName;
	}

	public function updateFormLayoutSetDescription($ptComposerFormLayoutSetDescription) {
		$db = Loader::db();
		$db->Execute('update PageTypeComposerFormLayoutSets set ptComposerFormLayoutSetDescription = ? where ptComposerFormLayoutSetID = ?', array(
			$ptComposerFormLayoutSetDescription, $this->ptComposerFormLayoutSetID
		));
		$this->ptComposerFormLayoutSetDescription = $ptComposerFormLayoutSetDescription;
	}


	public function updateFormLayoutSetDisplayOrder($displayOrder) {
		$db = Loader::db();
		$db->Execute('update PageTypeComposerFormLayoutSets set ptComposerFormLayoutSetDisplayOrder = ? where ptComposerFormLayoutSetID = ?', array(
			$displayOrder, $this->ptComposerFormLayoutSetID
		));
		$this->ptComposerFormLayoutSetDisplayOrder = $displayOrder;
	}

	public function delete() {
		$controls = FormLayoutSetControl::getList($this);
		foreach($controls as $control) {
			$control->delete();
		}
		$db = Loader::db();
		$db->Execute('delete from PageTypeComposerFormLayoutSets where ptComposerFormLayoutSetID = ?', array($this->ptComposerFormLayoutSetID));
		$pagetype = $this->getPageTypeObject();
		$pagetype->rescanFormLayoutSetDisplayOrder();
	}

	public function rescanFormLayoutSetControlDisplayOrder() {
		$sets = FormLayoutSetControl::getList($this);
		$displayOrder = 0;
		foreach($sets as $s) {
			$s->updateFormLayoutSetControlDisplayOrder($displayOrder);
			$displayOrder++;
		}
	}

    public function duplicate(\Concrete\Core\Page\Type\Type $type)
    {
        // first, create a new set based on the current one.
        $set = $type->addPageTypeComposerFormLayoutSet(
            $this->getPageTypeComposerFormLayoutSetDisplayName(),
            $this->getPageTypeComposerFormLayoutSetDescription()
        );
        $controls = FormLayoutSetControl::getList($this);
        foreach($controls as $control) {
            $control->duplicate($set);
        }

    }


}
