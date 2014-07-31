<?

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;
use Concrete\Core\Mail\Service;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use FileImporter;
use FileVersion;
use View;
use Core;


class Icons extends DashboardPageController {

    public $helpers = array('form', 'concrete/asset_library', 'json');

    public function on_start() {
        parent::on_start();
        $view = View::getInstance();
        $view->requireAsset('core/colorpicker');
    }

	public function favicon_saved() {
		$this->set('message', t("Icons updated successfully."));
	}

	public function favicon_removed() {
		$this->set('message', t("Icon removed successfully."));	
	}

	public function iphone_icon_saved() {
		$this->set('message', t("iPhone icon updated successfully."));	
	}

	public function iphone_icon_removed() {
		$this->set('message', t("iPhone icon removed successfully."));	
	}

	public function modern_icon_saved() {
		$this->set('message', t('Windows 8 icon updated successfully.'));
	}

	public function modern_icon_removed() {
		$this->set('message', t('Windows 8 icon removed successfully.'));
	}

	function update_modern_thumbnail() {
		if($this->token->validate('update_modern_thumbnail')) {
            $S = Core::make('helper/security');
            $modernThumbFID = $S->sanitizeInt($this->post('modernThumbFID'));
            $modernThumbBG = $S->sanitizeString($this->post('modernThumbBG'));
            $result = "modern_icon_saved";
            if($modernThumbFID) {
                Config::save('MODERN_TILE_THUMBNAIL_FID', $modernThumbFID);
			} else {
                Config::save('MODERN_TILE_THUMBNAIL_FID', 0);
                $result = 'modern_icon_removed';
			}

            Config::save('MODERN_TILE_THUMBNAIL_BGCOLOR', $modernThumbBG);

            $this->redirect('/dashboard/system/basics/icons/', $result);
		}
		else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}

	function update_iphone_thumbnail(){
		if ($this->token->validate("update_iphone_thumbnail")) {

            $S = Core::make('helper/security');
            $faviconFID = $S->sanitizeInt($this->post('iosHomeFID'));

			if($faviconFID){
				Config::save('IPHONE_HOME_SCREEN_THUMBNAIL_FID', $faviconFID);
                $this->redirect('/dashboard/system/basics/icons/', 'iphone_icon_saved');
            } else {
                Config::save('IPHONE_HOME_SCREEN_THUMBNAIL_FID', 0);
                $this->redirect('/dashboard/system/basics/icons/', 'iphone_icon_removed');
            }

		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}
	
	function update_favicon(){
		if ($this->token->validate("update_favicon")) {
            $S = Core::make('helper/security');
            $faviconFID = $S->sanitizeInt($this->post('faviconFID'));

            if($faviconFID) {
                Config::save('FAVICON_FID', $faviconFID);
                $this->redirect('/dashboard/system/basics/icons/', 'favicon_saved');
            } else {
                Config::save('FAVICON_FID',0);
                $this->redirect('/dashboard/system/basics/icons/', 'favicon_removed');
            }
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}

}