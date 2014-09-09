<?php
namespace Concrete\Core\Page\Type\PublishTarget\Configuration;
use Concrete\Core\Page\Type\Type;
use Loader;
use Page;
class ParentPageConfiguration extends Configuration {

	protected $cParentID;
	
	public function setParentPageID($cParentID) {
		$this->cParentID = $cParentID;
	}

	/** 
	 * Note: if a configuration contains this method, it is assumed that the configuration will default to this page and
	 * can skip composer
	 */
	public function getDefaultParentPageID() {
		return $this->getParentPageID();
	}
	
	public function getParentPageID() {
		return $this->cParentID;
	}

	public function getPageTypePublishTargetConfiguredTargetParentPageID() {
		return $this->cParentID;
	}

	public function export($cxml) {
		$target = parent::export($cxml);
		$c = Page::getByID($this->cParentID);
		if (is_object($c) && !$c->isError()) {
			$target->addAttribute('path', $c->getCollectionPath());
		}
	}

    public function canPublishPageTypeBeneathTarget(Type $pagetype, \Concrete\Core\Page\Page $page)
    {
        return $page->getCollectionID() == $this->getParentPageID();
    }


}