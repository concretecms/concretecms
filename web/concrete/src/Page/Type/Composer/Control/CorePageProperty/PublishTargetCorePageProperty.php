<?php
namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;
use Loader;
class PublishTargetCorePageProperty extends CorePageProperty {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('publish_target');
		$this->setPageTypeComposerControlName(t('Page Location'));
		$this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/attributes/image_file/icon.png');
	}

	public function pageTypeComposerFormControlSupportsValidation() {
		return false;
	}

	public function getPageTypeComposerControlDraftValue() {
		if (is_object($this->page)) {
			return $this->page->getPageDraftTargetParentPageID();
		}
	}
	


}