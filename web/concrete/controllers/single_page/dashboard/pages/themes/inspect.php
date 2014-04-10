<?
namespace Concrete\Controller\SinglePage\Dashboard\Pages\Themes;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Loader;
use PageTheme;
use Package;
use PageTemplate;
use Exception;

class Inspect extends DashboardPageController {

	protected $helpers = array('html');

	// grab all the page types from within a theme	
	public function view($pThemeID = null, $isOnInstall = false) {
		if (!$pThemeID) {
			$this->redirect('/dashboard/pages/themes/');
		}
		
		$v = Loader::helper('validation/error');
		$pt = PageTheme::getByID($pThemeID);
		if (is_object($pt)) {
			$files = $pt->getFilesInTheme();
			$this->set('files', $files);
			$this->set('pThemeID', $pThemeID);
			$this->set('pageTheme', $pt);
		} else {
			$v->add('Invalid Theme');
		}	
		
		if ($isOnInstall) {
			$this->set('message', t("Theme installed. You may automatically create page types from template files contained in your theme using the form below."));
		}
		
		if ($v->has()) {
			$this->set('error', $v);
		}

		$this->set('disableThirdLevelNav', true);

	}
	
	public function activate_files($pThemeID) {
		try {
			$pt = PageTheme::getByID($pThemeID);
			$txt = Loader::helper('text');
			if (!is_array($this->post('pageTemplates'))) {
				throw new Exception(t("You must specify at least one template to create."));
			}
			
			$pkg = false;
			$pkgHandle = $pt->getPackageHandle();
			if ($pkgHandle) {
				$pkg = Package::getByHandle($pkgHandle);
			}

			foreach($this->post('pageTemplates') as $pTemplateHandle) {
				$pTemplateName = $txt->unhandle($pTemplateHandle);
				$pTemplateIcon = FILENAME_PAGE_TEMPLATE_DEFAULT_ICON;
				$ct = PageTemplate::add($pTemplateHandle, $pTemplateName, $pTemplateIcon, $pkg);
			}
			$this->set('success', t('Files in the theme were activated successfully.'));
		} catch(Exception $e) {
			$this->set('error', $e);
		}
		$this->view($pThemeID);
	}


	

}

?>