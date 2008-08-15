<?

class DashboardSettingsController extends Controller {

	var $helpers = array('form');
	
	public function set_personal($saved = false) {
		$u = new User();
		$this->set('ui_breadcrumb', $u->config('UI_BREADCRUMB'));
		if ($saved) {
			$this->set('message', 'Settings configuration saved.');
		}

	}

	public function set_global($updated = false) {
		$debug_level = Config::get('SITE_DEBUG_LEVEL');
		if ($debug_level < 1) {
			$debug_level = DEBUG_DISPLAY_PRODUCTION;
		}
		$site_maintenance_mode = Config::get('SITE_MAINTENANCE_MODE');
		if ($site_maintenance_mode < 1) {
			$site_maintenance_mode = 0;
		}
		
		$this->set('debug_level', $debug_level);		
		$this->set('site_maintenance_mode', $site_maintenance_mode);
		$this->set('url_rewriting', URL_REWRITING);
		$this->set('site', SITE);
		
		if ($updated) {
			switch($updated) {
				case "maintenance_enabled";
					$this->set('message', 'Maintenance Mode turned on. Your site is <b>now private</b>.');	
					break;
				case "maintenance_disabled";
					$this->set('message', 'Maintenance Mode turned off. Your site is <b>public</b>.');	
					break;
				case "sitename_saved";
					$this->set('message', 'Your site\'s name has been saved.');	
					break;
				case "debug_saved";
					$this->set('message', 'Debug configuration saved.');	
					break;
				case "rewriting_saved";
					if (URL_REWRITING) {
						$this->set('message', 'URL rewriting enabled. Make sure you copy the lines below these URL Rewriting settings area and place them in your .htaccess or web server configuration file.');
					} else {
						$this->set('message', 'URL rewriting disabled.');
					}
					break;		
			}
		}
	}

	public function update_maintenance() {
		if ($this->isPost()) {
			Config::save('SITE_MAINTENANCE_MODE', $this->post('site_maintenance_mode'));
			if ($this->post('site_maintenance_mode') == 1) { 
				$this->redirect('/dashboard/settings','set_global','maintenance_enabled');
			} else {
				$this->redirect('/dashboard/settings','set_global','maintenance_disabled');
			}
		}
	}

	public function update_sitename() {
		if ($this->isPost()) {
			Config::save('SITE', $this->post('SITE'));
			$this->redirect('/dashboard/settings','set_global','sitename_saved');
		}
	}

	public function update_user_settings() {
		if ($this->isPost()) {
			$u = new User();
			$u->saveConfig('UI_BREADCRUMB', $this->post('ui_breadcrumb'));
			$this->redirect('/dashboard/settings','set_personal', true);
		}
	}

	public function update_debug() {
		if ($this->isPost()) {
			Config::save('SITE_DEBUG_LEVEL', $this->post('debug_level'));
			$this->redirect('/dashboard/settings','set_global','debug_saved');
		}
	}

	public function update_rewriting() {
		if ($this->isPost()) {
			Config::save('URL_REWRITING', $this->post('URL_REWRITING'));
			$this->redirect('/dashboard/settings','set_global','rewriting_saved');
		}
	}
	
	
	public function on_start() {
		$this->set_groups_and_home();
		$prefsSelected = false;
		$globalSelected = false;
		$permsSelected = false;				
		switch($this->getTask()) {
			case "set_personal":
				$prefsSelected = true;
				break;
			case "set_global":
				$globalSelected = true;
				break;
			default:
				$permsSelected = true;
				break;
		}					
		$subnav = array(
			array(View::url('/dashboard/settings'), 'Permissions and Access', $permsSelected),
			array(View::url('/dashboard/settings', 'set_personal'), 'Personal Editing Preferences', $prefsSelected),
			array(View::url('/dashboard/settings', 'set_global'), 'Global Settings', $globalSelected)
		);
		$this->set('subnav', $subnav);
	}
	
	protected function set_groups_and_home() {
		if (PERMISSIONS_MODEL != 'simple') {
			return;
		}
		
		$home = Page::getByID(1, "RECENT");
		$gl = new GroupList($home);
		$gArrayTmp = $gl->getGroupList();
		$gArray = array();
		foreach($gArrayTmp as $gi) {
			if ($gi->getGroupID() == GUEST_GROUP_ID) {
				$ggu = $gi;
			} else if ($gi->getGroupID() == REGISTERED_GROUP_ID) {
				$gru = $gi;
			} else {
				$gArray[] = $gi;
			}
		}
		
		$this->set('ggu', $ggu);
		$this->set('gru', $gru);
		$this->set('gArray', $gArray);
		$this->set('home', $home);
	}
	
	public function update_permissions() {

		$home = $this->get('home');
		$gru = $this->get('gru');
		$ggu = $this->get('ggu');

		$args = array();
		switch($_POST['view']) {
			case "ANYONE":
				$args['collectionRead'][] = 'gID:' . $ggu->getGroupId(); // this API is pretty crappy. TODO: clean this up in a nice object oriented fashion
				break;
			case "USERS":
				$args['collectionRead'][] = 'gID:' . $gru->getGroupId(); // this API is pretty crappy. TODO: clean this up in a nice object oriented fashion
				break;
			case "PRIVATE":
				$args['collectionRead'] = array(); // no one can view it (except the super admin, who can always view)
				break;
			
		}
		
		$args['collectionWrite'] = array();
		if (is_array($_POST['gID'])) {
			foreach($_POST['gID'] as $gID) {
				$args['collectionWrite'][] = 'gID:' . $gID;
				$args['collectionAdmin'][] = 'gID:' . $gID;
				$args['collectionDelete'][] = 'gID:' . $gID;
			}
		}
		
		$args['cInheritPermissionsFrom'] = 'OVERRIDE';
		$args['cOverrideTemplatePermissions'] = 1;
		
		$home->updatePermissions($args);
		
		$this->redirect('/dashboard/settings/', 'permissions_updated');
	}
	
	public function permissions_updated() {
		$this->set('message', 'Permissions saved.');
	}

}