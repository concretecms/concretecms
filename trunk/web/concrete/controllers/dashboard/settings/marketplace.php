<?

defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardSettingsMarketplaceController extends Controller {

	var $helpers = array('form'); 

	public function on_start() {
		Loader::library('marketplace');
		$subnav = array(
			array(View::url('/dashboard/settings'), t('General'), true),
			array(View::url('/dashboard/settings/mail'), t('Email')),
			array(View::url('/dashboard/settings', 'set_permissions'), t('Access')),
			array(View::url('/dashboard/settings', 'set_developer'), t('Debug')),
			array(View::url('/dashboard/settings', 'manage_attribute_types'), t('Attributes'))
		);
		$this->set('subnav', $subnav);
	}
	
	public function view() {
		if (!Marketplace::isConnected()) {
			$url = MARKETPLACE_URL_CONNECT;
			$csToken = Marketplace::generateSiteToken();
			$this->set('url', $url . '?ts=' . time() . '&csToken=' . $csToken . '&csName=' . htmlspecialchars(SITE, ENT_QUOTES, APP_CHARSET));
		}
	}

}