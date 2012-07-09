<?

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Pages_Themes_Customize extends Controller {

	protected $helpers = array('html', 'form');

	public function view($themeID = false, $state = false) {
		$pt = PageTheme::getByID($themeID);
		if ($themeID == false || (!is_object($pt))) {
			$this->redirect('/dashboard/pages/themes');
		}
		if (is_object($pt)) {
			$styles = $pt->getEditableStylesList();
			$this->set('t', $pt);
			$this->set('styles', $styles);
			$this->set('themeID', $themeID);
		 	Cache::delete('preview_theme_style', $themeID);
		}
		$subnav = array(
			array(View::url('/dashboard/pages/themes/'), '&lt; ' . t('Return to Themes'))
		);
		$this->set('subnav', $subnav);		
		if ($state == 'saved') {
			$this->set('message', t('Theme styles updated.'));
		} else if ($state == 'reset') {
			$this->set('message', t('This theme has been reset.'));
		}
	}
	
	public function save() {
		$vt = Loader::helper('validation/token');
		if ($vt->validate()) {
			$themeID = $this->post('themeID');
			$pt = PageTheme::getByID($themeID);
			// values will be an associative array of key/values that will be passed
			// to the stylesheet. Stuff like
			// $values['background_color'] = '#ffffff';
			// This will then be merged. All values will be looped through and populatd in the stylesheet in place of the old values.
			if (is_object($pt)) {
				$values = $pt->mergeStylesFromPost($this->post());
				$pt->saveEditableStyles($values);
				$this->view($themeID);
				$this->redirect('/dashboard/pages/themes/customize', 'view', $themeID, 'saved');
			}
		}
	}
	
	public function reset() {
		$vt = Loader::helper('validation/token');
		if ($vt->validate()) {
			$themeID = $this->post('themeID');
			$pt = PageTheme::getByID($themeID);
			if (is_object($pt)) {
				$values = $pt->reset();
				$this->redirect('/dashboard/pages/themes/customize', 'view', $themeID, 'reset');
			}
		}
	}
	



}

?>