<?
namespace Concrete\Controller\SinglePage\Dashboard\System\Optimization;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Cache\Page\PageCache;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Core;

class Clearcache extends DashboardPageController {
	
	public $helpers = array('form'); 
	
	public function view(){
	}
	
	public function do_clear() {
		if ($this->token->validate("clear_cache")) {
			if ($this->isPost()) {
                Core::make('cache')->flush();
                Core::make('cache/expensive')->flush();

                // flush the CSS cache
                if (is_dir(DIR_FILES_CACHE . '/' . DIRNAME_CSS)) {
                    $fh = Loader::helper("file");
                    $fh->removeAll(DIR_FILES_CACHE . '/' . DIRNAME_CSS);
                }

                $pageCache = PageCache::getLibrary();
                if (is_object($pageCache)) {
                    $pageCache->flush();
                }

                // clear the environment overrides cache
                $env = \Environment::get();
                $env->clearOverrideCache();

                // clear block type cache
                BlockType::clearCache();
				$this->redirect('/dashboard/system/optimization/clearcache', 'cache_cleared');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}

	public function cache_cleared() {
		$this->set('message', t('Cached files removed.'));	
		$this->view();
	}
	
	
}
