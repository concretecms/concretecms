<?php
namespace Concrete\Core\Page\Type\Composer\Control\CorePageProperty;
class DateTimeCorePageProperty extends CorePageProperty {
	
	public function __construct() {
		$this->setCorePagePropertyHandle('date_time');
		$this->setPageTypeComposerControlName(t('Public Date/Time'));
		$this->setPageTypeComposerControlIconSRC(ASSETS_URL . '/models/attribute/types/date_time/icon.png');
	}

	public function publishToPage(Page $c, $data, $controls) {
		$this->addPageTypeComposerControlRequestValue('cDatePublic', Loader::helper('form/date_time')->translate('date_time', $data));
		parent::publishToPage($c, $data, $controls);
	}

	public function validate() {
		$e = Loader::helper('validation/error');
		$date = $this->getPageTypeComposerControlDraftValue();
		if (!strtotime($date)) {
			$e->add(t('You must specify a valid date/time for this page.'));
			return $e;
		}
	}

	public function getPageTypeComposerControlDraftValue() {
		$c = $this->page;
		return $c->getCollectionDatePublic();
	}
	


}