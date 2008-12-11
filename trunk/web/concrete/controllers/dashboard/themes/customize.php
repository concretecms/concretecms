<?

defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardThemesCustomizeController extends Controller {

	protected $helpers = array('html', 'form');

	public function view($themeID, $isSaved = false) {
		$pt = PageTheme::getByID($themeID);
		if (is_object($pt)) {
			$styles = $pt->getEditableStylesList();
			$this->set('styles', $styles);
			$this->set('themeID', $themeID);
		 	Cache::delete('preview_theme', $themeID);
		}
		$subnav = array(
			array(View::url('/dashboard/themes/'), '&lt; ' . t('Return to Themes'))
		);
		$this->set('subnav', $subnav);		
		if ($isSaved) {
			$this->set('message', t('Editable styles saved.'));
		}
	}
	
	public function save() {
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
			$this->redirect('/dashboard/themes/customize', 'view', $themeID, 'saved');
		}
	}
	


}

?>