<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;
use Concrete\Core\Cache\Page\PageCache;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Core;

class Codes extends DashboardPageController {

	public function view() {
		$this->set('tracking_code', Config::get('concrete.seo.tracking.code'));
		$tracking_code_position = Config::get('concrete.seo.tracking.code_position');
		if (!$tracking_code_position) {
			$tracking_code_position = 'bottom';
		}
		$this->set('tracking_code_position', $tracking_code_position);

		if ($this->isPost()) {
			if ($this->token->validate('update_tracking_code')) {
					Config::save('concrete.seo.tracking.code', $this->post('tracking_code'));
					Config::save('concrete.seo.tracking.code_position', $this->post('tracking_code_position'));
                    $pageCache = PageCache::getLibrary();
                    if (is_object($pageCache)) {
                        $pageCache->flush();
                    }
					$this->redirect('/dashboard/system/seo/codes', 'saved');
			} else {
				$this->error->add($this->token->getErrorMessage());
			}
		}
	}

	public function saved() {
		$this->set('message', implode(PHP_EOL, array(
			t('Tracking code settings updated successfully.'),
			t('Cached files removed.')
		)));
		$this->view();
	}
}
