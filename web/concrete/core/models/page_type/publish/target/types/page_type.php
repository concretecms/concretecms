<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_PageTypePageTypePublishTargetType extends PageTypePublishTargetType {


	public function configurePageTypePublishTarget(PageType $pt, $post) {
		$configuration = new PageTypePageTypePublishTargetConfiguration($this);
		$configuration->setPageTypeID($post['ptID']);
		return $configuration;
	}

	public function configurePageTypePublishTargetFromImport($txml) {
		$configuration = new PageTypePageTypePublishTargetConfiguration($this);
		$ct = PageType::getByHandle((string) $txml['pagetype']);
		$configuration->setPageTypeID($ct->getPageTypeID());
		return $configuration;
	}

	
}