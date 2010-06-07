<?php 

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
	
	public function view($isNew = false) {
		$this->set('isNew', $isNew);
	}
	
	public function connect_complete() {
		if (!$_POST['csToken']) {
			$this->set('error', t('An unexpected error occurred when connecting your site to the marketplace.'));
		} else {
			Config::save('MARKETPLACE_SITE_TOKEN', $_POST['csToken']);
			Config::save('MARKETPLACE_SITE_URL_TOKEN', $_POST['csURLToken']);
			print '<script type="text/javascript">parent.window.location.href=\'' . View::url('/dashboard/install', 'view', 'community_connect_success') . '\';</script>';
			exit;
		}
	}

}