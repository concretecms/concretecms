<?php 

namespace Concrete\Core\Page\Type\PublishTarget\Configuration;
class PageTypeConfiguration extends Configuration {

	protected $ptID;

	public function setPageTypeID($ptID) {
		$this->ptID = $ptID;
	}

	public function getPageTypeID() {
		return $this->ptID;
	}

	public function export($cxml) {
		$target = parent::export($cxml);
		$ct = PageType::getByID($this->ptID);
		if (is_object($ct)) {
			$target->addAttribute('pagetype', $ct->getPageTypeHandle());
		}
	}
	
}