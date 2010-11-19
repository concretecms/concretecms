<?php 

defined('C5_EXECUTE') or die("Access Denied.");
class DashboardSettingsController extends Controller {

	var $helpers = array('form'); 

	protected function getRewriteRules() {
		$rewriteRules = "<IfModule mod_rewrite.c>\n";
		$rewriteRules .= "RewriteEngine On\n";
		$rewriteRules .= "RewriteBase " . DIR_REL . "/\n";
		$rewriteRules .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
		$rewriteRules .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
		$rewriteRules .= "RewriteRule ^(.*)$ " . DISPATCHER_FILENAME . "/$1 [L]\n";
		$rewriteRules .= "</IfModule>";
		return $rewriteRules;
	}

	public function access_task_permissions() {
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.dashboard.permissions.js'));	
	}
	
	public function save_task_permissions() {
		if (!$this->token->validate("update_permissions")) {
			$this->set('error', array($this->token->getErrorMessage()));
			return;
		}	
		
		$tp = new TaskPermission();
		if (!$tp->canAccessTaskPermissions()) {
			$this->set('error', array(t('You do not have permission to modify these items.')));
			return;
		}
		
		$post = $this->post();
		
		$h = Loader::helper('concrete/dashboard/task_permissions');
		$h->save($post);
		$this->redirect('/dashboard/settings/', 'set_permissions', 'task_permissions_saved');
	
	}
	
	public function view($updated = false, $aux = false) {
		$u = new User();		
		 
		$this->set('site_tracking_code', Config::get('SITE_TRACKING_CODE') );		
		$this->set('url_rewriting', URL_REWRITING);
		$this->set('marketplace_enabled_in_config', Config::get('ENABLE_MARKETPLACE_SUPPORT') );		
		$this->set('site', SITE);
		$this->set('ui_breadcrumb', $u->config('UI_BREADCRUMB'));
		$this->set('ui_filemanager', $u->config('UI_FILEMANAGER'));
		$this->set('ui_sitemap', $u->config('UI_SITEMAP'));
		$this->set('api_key_picnik', Config::get('API_KEY_PICNIK'));
		
		$txtEditorMode = Config::get('CONTENTS_TXT_EDITOR_MODE');
		$this->set( 'txtEditorMode', $txtEditorMode ); 
		$this->set('rewriteRules', $this->getRewriteRules());
		$textEditorWidth = Config::get('CONTENTS_TXT_EDITOR_WIDTH');
		$this->set( 'textEditorWidth', $textEditorWidth );
		$textEditorHeight = Config::get('CONTENTS_TXT_EDITOR_HEIGHT');
		$this->set( 'textEditorHeight', $textEditorHeight );		
				
		$txtEditorCstmCode=Config::get('CONTENTS_TXT_EDITOR_CUSTOM_CODE');
		if( !strlen($txtEditorCstmCode) || $txtEditorMode!='CUSTOM' )
			$txtEditorCstmCode=$this->get_txt_editor_default();
		$this->set('txtEditorCstmCode', $txtEditorCstmCode );
		
		Loader::library('marketplace');
		$mi = Marketplace::getInstance();
		if ($mi->isConnected()) {
			$this->set('marketplacePageURL', Marketplace::getSitePageURL());
		}
		if ($updated) {
			switch($updated) {
				case 'statistics_saved':
					$this->set('message', t('Statistics tracking preference saved.'));
					break;
				case "tracking_code_saved";
					$this->set('message', t('Your tracking code has been saved.'));	
					break;			
				/*
				//moved to set_permissions
				case "maintenance_enabled";
					$this->set('message', t('Maintenance Mode turned on. Your site is now private.'));	
					break;
				case "maintenance_disabled":
					$this->set('message', t('Maintenance Mode turned off. Your site is public.'));	
					break;
				*/
				case "marketplace_turned_on";
					$this->set('message', t('Marketplace support is now enabled.'));	
					break;
				case "marketplace_turned_off":
					$this->set('message', t('Marketplace support is now disabled.'));	
					break;				
				case "favicon_saved":  
					$this->set('message', t('Bookmark icon saved.'));	
					break;
				case "favicon_removed":  
					$this->set('message', t('Bookmark icon removed.'));	
					break;							
				case "editing_preferences_saved":
					$this->set('message', t('Editing preferences saved.'));	
					break;
				case "sitename_saved":
					$this->set('message', t("Your site's name has been saved."));	
					break;
				case "image_editing_saved":
					$this->set('message', t("Image editing options have been saved."));	
					break;
				case "debug_saved":
					$this->set('message', t('Debug configuration saved.'));
					break;
				case "cache_cleared";
					$this->set('message', t('Cached files removed.'));	
					break;
				case "cache_updated";
					$this->set('message', t('Cache settings saved.'));	
					break;
				case "txt_editor_config_saved":
					$this->set('message', t('Content text editor settings saved.'));
					break;														
				case "rewriting_saved":
					if (URL_REWRITING) {
						if ($aux == 0) {
							$this->set('message', t('URL rewriting enabled. Make sure you copy the lines below these URL Rewriting settings area and place them in your .htaccess or web server configuration file.'));
						} else {
							$this->set('message', t('URL rewriting enabled. .htaccess file updated.'));
						}
					} else {
						$this->set('message', t('URL rewriting disabled.'));
					}
					break;		
			}
		}
	}
	
	public function update_maintenance() { 
		$this->set_permissions();
		if ($this->token->validate("update_maintenance")) {
			if ($this->isPost()) {
				Config::save('SITE_MAINTENANCE_MODE', $this->post('site_maintenance_mode'));
				if ($this->post('site_maintenance_mode') == 1) { 
					$this->redirect('/dashboard/settings','set_permissions',"maintenance_enabled");
				} else {
					$this->redirect('/dashboard/settings','set_permissions',"maintenance_disabled");
				}
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}

	public function formatTimestampAsMinutesSeconds($seconds){
		if ($seconds == 0) {
			return t('Never');
		}
		else{
			$seconds = $seconds-time();
			return floor($seconds / 60) . 'm' . $seconds % 60 . 's';
		}
		
	}
	
	public function update_tracking_code() {
		if ($this->token->validate("update_tracking_code")) {
			if ($this->isPost()) {
				Config::save('SITE_TRACKING_CODE', $this->post('tracking_code'));
				$this->redirect('/dashboard/settings','tracking_code_saved'); 			
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}	
	
	public function export_database_schema() {
		if (!ENABLE_DEVELOPER_OPTIONS) { 
			return false;
		}
		$db = Loader::db();
		$ab = $db->getADOSChema();
		$xml = $ab->ExtractSchema();
		$this->set('schema', $xml);
	}
	
	public function refresh_database_schema() {
		if ($this->token->validate("refresh_database_schema")) {
			$msg = '';
			if ($this->post('refresh_global_schema')) {
				// refresh concrete/config/db.xml and all installed blocks
				$cnt = Loader::controller("/upgrade");
				try {
					$cnt->refresh_schema();
					$msg .= t('Core database files and installed blocks refreshed.');
				} catch(Exception $e) {
					$this->set('error', $e);
				}
			}
			
			if ($this->post('refresh_local_schema')) {
				// refresh concrete/config/db.xml and all installed blocks
				if (file_exists('config/' . FILENAME_LOCAL_DB)) {
					try {
						Package::installDB(DIR_BASE . '/config/' . FILENAME_LOCAL_DB);
						$msg .= ' ' . t('Local database file refreshed.');
					} catch(Exception $e) {
						$this->set('error', $e);
					}					
				}
			}
			
			$msg = trim($msg);
			$this->set('message', $msg);

		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}

	}
	
	const IP_BLACKLIST_CHANGE_MAKEPERM		 	= 1;
	const IP_BLACKLIST_CHANGE_REMOVE		 	= 2;
	const IP_BAN_LOCK_IP_HOW_LONG_TYPE_TIMED 	= 'timed';
	const IP_BAN_LOCK_IP_HOW_LONG_TYPE_FOREVER 	= 'forever';	
	public function update_ipblacklist() {
		$db = Loader::db();
		if ($this->token->validate("update_ipblacklist")) {	
			Loader::model('user_banned_ip');
			//configs from top part form
			$ip_ban_lock_ip_enable = (1 == $this->post('ip_ban_lock_ip_enable')) ? 1 : 0;
			Config::save('IP_BAN_LOCK_IP_ENABLE',$ip_ban_lock_ip_enable);
			Config::save('IP_BAN_LOCK_IP_ATTEMPTS',$this->post('ip_ban_lock_ip_attempts'));
			Config::Save('IP_BAN_LOCK_IP_TIME',$this->post('ip_ban_lock_ip_time'));
			
			if (self::IP_BAN_LOCK_IP_HOW_LONG_TYPE_FOREVER != $this->post('ip_ban_lock_ip_how_long_type')) {
				Config::Save('IP_BAN_LOCK_IP_HOW_LONG_MIN',$this->post('ip_ban_lock_ip_how_long_min'));							
			}
			else {
				Config::Save('IP_BAN_LOCK_IP_HOW_LONG_MIN',0);	
			}
			
			//ip table actions
			//use a single sql query, more efficient than active record
			$ip_ban_changes = $this->post('ip_ban_changes');
			if (count($ip_ban_changes) > 0) {				
				$ip_ban_change_to 	= $this->post('ip_ban_change_to');				
				$q = 'UPDATE UserBannedIPs SET expires = ? WHERE ';
				$v = array();
				switch ($ip_ban_change_to) {
					case self::IP_BLACKLIST_CHANGE_MAKEPERM:	
						$v[] = 0;			//expires 0 is a perma-ban
						break;
					case self::IP_BLACKLIST_CHANGE_REMOVE:
						$v[] = 1;			//expires 1 is really far in past, defacto expire
						break;
				}				
							
				$utility			= new UserBannedIP();
				foreach($ip_ban_changes as $key){
					$q .= '(ipFrom = ? AND ipTo = ?) OR';
					$ids = $utility->parseUniqueID($key);
					$v[] = $ids['ipFrom'];
					$v[] = $ids['ipTo'];
				}
				$q = substr($q,0,strlen($q)-3);				
				$db->execute($q,$v);
			}

			//textarea actions
			$ip_ranges = $this->parseIPBlacklistIntoRanges();
			$db = Loader::db();
			$db->StartTrans();
			$q = 'DELETE FROM UserBannedIPs WHERE isManual = 1';
			$db->execute($q);
			//no batch insert in adodb? :(
			
			foreach ($ip_ranges as $ip_range) {			
				$ip = new UserBannedIP();		
				
				$ip->ipFrom 	= ip2long($ip_range['ipFrom']);
				$ip->ipTo		= $ip_range['ipTo'];
				if ($ip->ipTo != 0) {
					echo $ip->ipTo . "\n";
					$ip->ipTo		= ip2long($ip_range['ipTo']);
				}					
				$ip->banCode	= UserBannedIP::IP_BAN_CODE_REGISTRATION_THROTTLE;
				$ip->expires	= 0;
				$ip->isManual	= 1;			
				try{
					$ip->save();
				}
				catch (Exception $e) {
					//silently discard duplicates
				}
			}
			$db->CompleteTrans();
			
			$this->redirect('/dashboard/settings','set_permissions','saved_ipblacklist');
		}
		else {			
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}
	
	//assumes ipv4
	private function parseIPBlacklistIntoRanges() {
		$ips = preg_split('{[\r\n]+}', $this->post('ip_ban_manual'),null, PREG_SPLIT_NO_EMPTY  );
		$ip_ranges = Array();
		foreach ($ips as $ip) {
			if(strpos($ip, '*') === false){		
				$ip = long2ip(ip2long($ip));	//ensures a valid ip
				$ip_ranges[] = Array('ipFrom'=>$ip,'ipTo'=>0);
			}
			else{
				$aOctets = preg_split('{\.}',$ip);
				$ipFrom = '';
				$ipTo 	= '';
				for($i=0;$i<4;$i++){
					if(is_numeric($aOctets[$i])){
						$ipFrom .= $aOctets[$i].'.';
						$ipTo 	.= $aOctets[$i].'.';					
					}
					else{
						$ipFrom .= '0'.'.';
						$ipTo 	.= '255'.'.';										
					}
				}
				$ipFrom	= substr($ipFrom,0,strlen($ipFrom)-1);
				$ipTo	= substr($ipTo,0,strlen($ipTo)-1);		
				
				$ipFrom  = long2ip(ip2long($ipFrom)); //ensures a valid ip
				$ipTo  	 = long2ip(ip2long($ipTo));   //ensures a valid ip
				
				$ip_ranges[] = Array('ipFrom'=>$ipFrom,'ipTo'=>$ipTo);
			}
		}
		
		return $ip_ranges;
	}
	
	public function update_sitename() {
		if ($this->token->validate("update_sitename")) {
			if ($this->isPost()) {
				Config::save('SITE', $this->post('SITE'));
				$this->redirect('/dashboard/settings','sitename_saved');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}

	public function update_image_editing() {
		if ($this->token->validate("update_image_editing")) {
			if ($this->isPost()) {
				Config::save('API_KEY_PICNIK', $this->post('API_KEY_PICNIK'));
				$this->redirect('/dashboard/settings','image_editing_saved');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}

	public function clear_cache() {
		if ($this->token->validate("clear_cache")) {
			if ($this->isPost()) {
				if (Cache::flush()) {
					$this->redirect('/dashboard/settings', 'cache_cleared');
				}
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}

	public function update_cache() {
		if ($this->token->validate("update_cache")) {
			if ($this->isPost()) {
				$u = new User();
				$eca = $this->post('ENABLE_CACHE') == 1 ? 1 : 0; 
				Cache::flush();
				Config::save('ENABLE_CACHE', $eca);
				Config::save('FULL_PAGE_CACHE_GLOBAL', $this->post('FULL_PAGE_CACHE_GLOBAL'));
				Config::save('FULL_PAGE_CACHE_LIFETIME', $this->post('FULL_PAGE_CACHE_LIFETIME'));
				Config::save('FULL_PAGE_CACHE_LIFETIME_CUSTOM', $this->post('FULL_PAGE_CACHE_LIFETIME_CUSTOM'));				
				$this->redirect('/dashboard/settings', 'cache_updated');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}

	public function update_user_settings() {
		if ($this->token->validate("update_user_settings")) {
			if ($this->isPost()) {
				$u = new User();
				$u->saveConfig('UI_BREADCRUMB', $this->post('ui_breadcrumb'));
				$u->saveConfig('UI_FILEMANAGER', $this->post('ui_filemanager'));
				$u->saveConfig('UI_SITEMAP', $this->post('ui_sitemap'));
				$this->redirect('/dashboard/settings','editing_preferences_saved');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}

	public function update_debug() {
		if ($this->token->validate("update_debug")) {
			if ($this->isPost()) {
				Config::save('SITE_DEBUG_LEVEL', $this->post('debug_level'));
				$this->redirect('/dashboard/settings','set_developer','debug_saved');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}

	public function update_logging() {
		if ($this->token->validate("update_logging")) {
			if ($this->isPost()) {
				$elem = $this->post('ENABLE_LOG_EMAILS') == 1 ? 1 : 0;
				$eler = $this->post('ENABLE_LOG_ERRORS') == 1 ? 1 : 0;
				
				Config::save('ENABLE_LOG_EMAILS', $elem);
				Config::save('ENABLE_LOG_ERRORS', $eler);
				$this->redirect('/dashboard/settings','set_developer','logging_saved');
			}
		} else {
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}

	public function update_rewriting() {
		$this->set('rewriteRules', $this->getRewriteRules());
		$start = '# -- concrete5 urls start --';
		$end = '# -- concrete5 urls end --';
		$rules = $start . "\n" . $this->getRewriteRules() . "\n" . $end;
		$htu = 0;
		if ($this->isPost()) {
			Config::save('URL_REWRITING', $this->post('URL_REWRITING'));
			
			if ($this->post('URL_REWRITING') == 1) { 
				if (file_exists(DIR_BASE . '/.htaccess') && is_writable(DIR_BASE . '/.htaccess')) {		
					if (file_put_contents(DIR_BASE . '/.htaccess', "\n" . $rules, FILE_APPEND)) {
						$htu = 1;
					}
				} else if (!file_exists(DIR_BASE . '/.htaccess') && is_writable(DIR_BASE)) {		
					if (file_put_contents(DIR_BASE . '/.htaccess', $rules)) {
						$htu = 1;
					}
				}
			} else {
				if (file_exists(DIR_BASE . '/.htaccess') && is_writable(DIR_BASE . '/.htaccess')) {
					$fh = Loader::helper('file');
					$contents = $fh->getContents(DIR_BASE . '/.htaccess');
					if (file_put_contents(DIR_BASE . '/.htaccess', str_replace($rules, '', $contents))) {
						$htu = 1;
					}
				}
			}
			
			$this->redirect('/dashboard/settings','rewriting_saved', $htu);
		}
	}	
	
	public function update_statistics() {
		if ($this->isPost()) {
			$sv = $this->post('STATISTICS_TRACK_PAGE_VIEWS') == 1 ? 1 : 0;
			Config::save('STATISTICS_TRACK_PAGE_VIEWS', $sv);
			$this->redirect('/dashboard/settings','statistics_saved');
		}
	}	
	
	public function set_developer($updated = false) {
		$debug_level = Config::get('SITE_DEBUG_LEVEL');
		$enable_log_emails = Config::get('ENABLE_LOG_EMAILS');
		$enable_log_errors = Config::get('ENABLE_LOG_ERRORS');
		if ($debug_level < 1) {
			$debug_level = DEBUG_DISPLAY_PRODUCTION;
		}
		$this->set('debug_level', $debug_level);		
		$this->set('enable_log_emails', $enable_log_emails);		
		$this->set('enable_log_errors', $enable_log_errors);	
		
		
		if ($updated) {
			switch($updated) {
				case "debug_saved":
					$this->set('message', t('Debug configuration saved.'));
					break;
				case "logging_saved":
					$this->set('message', t('Logging configuration saved.'));	
					break;
				case "cache_cleared";
					$this->set('message', t('Cached files removed.'));	
					break;
				case "cache_updated";
					$this->set('message', t('Cache settings saved.'));	
					break;
			}
		}
	}
	
	
	public function get_environment_info() {
		set_time_limit(5);
		
		$environmentMessage = '# ' . t('concrete5 Version') . "\n" . APP_VERSION . "\n\n";
		$environmentMessage .= '# ' . t('concrete5 Packages') . "\n";
		$pla = PackageList::get();
		$pl = $pla->getPackages();
		$packages = array();
		foreach($pl as $p) {
			if ($p->isPackageInstalled()) {
				$packages[] =$p->getPackageName() . ' (' . $p->getPackageVersion() . ')';
			}			
		}
		if (count($packages) > 0) {
			natcasesort($packages);
			$environmentMessage .= implode(', ', $packages);
			$environmentMessage .= ".\n";
		} else {
			$environmentMessage .= t('None') . "\n";
		}
		$environmentMessage .= "\n";
		
		// overrides
		$environmentMessage .= '# ' . t('concrete5 Overrides') . "\n";
		$fh = Loader::helper('file');
		$overrides = array();
		$ovBlocks = $fh->getDirectoryContents(DIR_FILES_BLOCK_TYPES);
		$ovControllers = $fh->getDirectoryContents(DIR_FILES_CONTROLLERS);
		$ovElements = $fh->getDirectoryContents(DIR_FILES_ELEMENTS);
		$ovHelpers = $fh->getDirectoryContents(DIR_HELPERS);
		$ovJobs = $fh->getDirectoryContents(DIR_FILES_JOBS);
		$ovCSS = $fh->getDirectoryContents(DIR_BASE . '/' . DIRNAME_CSS);
		$ovJS = $fh->getDirectoryContents(DIR_BASE . '/' . DIRNAME_JAVASCRIPT);
		$ovLng = $fh->getDirectoryContents(DIR_BASE . '/' . DIRNAME_LANGUAGES);
		$ovLibs = $fh->getDirectoryContents(DIR_LIBRARIES);
		$ovMail = $fh->getDirectoryContents(DIR_FILES_EMAIL_TEMPLATES);
		$ovModels = $fh->getDirectoryContents(DIR_MODELS);
		$ovSingle = $fh->getDirectoryContents(DIR_FILES_CONTENT);
		$ovThemes = $fh->getDirectoryContents(DIR_FILES_THEMES);
		$ovTools = $fh->getDirectoryContents(DIR_FILES_TOOLS);

		foreach($ovBlocks as $ovb) {
			$overrides[] = DIRNAME_BLOCKS . '/' . $ovb;
		}
		foreach($ovControllers as $ovb) {
			$overrides[] = DIRNAME_CONTROLLERS . '/' . $ovb;
		}
		foreach($ovElements as $ovb) {
			$overrides[] = DIRNAME_ELEMENTS . '/' . $ovb;
		}
		foreach($ovHelpers as $ovb) {
			$overrides[] = DIRNAME_HELPERS . '/' . $ovb;
		}
		foreach($ovJobs as $ovb) {
			$overrides[] = DIRNAME_JOBS . '/' . $ovb;
		}
		foreach($ovJS as $ovb) {
			$overrides[] = DIRNAME_JAVASCRIPT . '/' . $ovb;
		}
		foreach($ovCSS as $ovb) {
			$overrides[] = DIRNAME_CSS . '/' . $ovb;
		}
		foreach($ovLng as $ovb) {
			$overrides[] = DIRNAME_LANGUAGES . '/' . $ovb;
		}
		foreach($ovLibs as $ovb) {
			$overrides[] = DIRNAME_LIBRARIES . '/' . $ovb;
		}
		foreach($ovMail as $ovb) {
			$overrides[] = DIRNAME_MAIL_TEMPLATES . '/' . $ovb;
		}
		foreach($ovModels as $ovb) {
			$overrides[] = DIRNAME_MODELS . '/' . $ovb;
		}
		foreach($ovSingle as $ovb) {
			$overrides[] = DIRNAME_PAGES . '/' . $ovb;
		}
		foreach($ovThemes as $ovb) {
			$overrides[] = DIRNAME_THEMES . '/' . $ovb;
		}
		foreach($ovTools as $ovb) {
			$overrides[] = DIRNAME_TOOLS . '/' . $ovb;
		}

		if (count($overrides) > 0) {
			$environmentMessage .= implode(', ', $overrides);
			$environmentMessage .= "\n";
		} else {
			$environmentMessage .= t('None') . "\n";
		}
		$environmentMessage .= "\n";

		print $environmentMessage;
		
		$environmentMessage = '# ' . t('Server Software') . "\n" . $_SERVER['SERVER_SOFTWARE'] . "\n\n";
		$environmentMessage .= '# ' . t('Server API') . "\n" . php_sapi_name() . "\n\n";
		$environmentMessage .= '# ' . t('PHP Version') . "\n" . PHP_VERSION . "\n\n";
		$environmentMessage .= '# ' . t('PHP Extensions') . "\n";
		if (function_exists('get_loaded_extensions')) {
			$gle = @get_loaded_extensions();
			natcasesort($gle);
			$environmentMessage .= implode(', ', $gle);
			$environmentMessage .= ".\n";
		} else {
			$environmentMessage .= t('Unable to determine.') . "\n";
		}

		print $environmentMessage;

		ob_start();
		phpinfo();
		$phpinfo = array('phpinfo' => array());
		if(preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER))
		foreach($matches as $match) {
			if(strlen($match[1])) {
				$phpinfo[$match[1]] = array();
			} else if(isset($match[3])) {
				$phpinfo[end(array_keys($phpinfo))][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
			} else {
				$phpinfo[end(array_keys($phpinfo))][] = $match[2];
			}
		}
		
		$environmentMessage = "\n# " . t('PHP Settings') . "\n";

		foreach($phpinfo as $name => $section) {
			foreach($section as $key => $val) {
				if (!preg_match('/.*limit.*/', $key) && !preg_match('/.*safe.*/', $key) && !preg_match('/.*max.*/', $key)) {
					continue;
				}
				if(is_array($val)) {
					$environmentMessage .= "$key - $val[0]\n";
				} else if(is_string($key)) {
					$environmentMessage .= "$key - $val\n";
				} else {
					$environmentMessage .= "$val\n";
				}
			}
		}
		
		print $environmentMessage;
		exit;
	}
	
	
	public function add_attribute_type() {
		$pat = PendingAttributeType::getByHandle($this->post('atHandle'));
		if (is_object($pat)) {
			$pat->install();
		}
		$this->redirect('dashboard/settings', 'manage_attribute_types', 'attribute_type_added');
	}
	
	public function save_attribute_type_associations($saved = false) {
		$list = AttributeKeyCategory::getList();
		foreach($list as $cat) {
			$cat->clearAttributeKeyCategoryTypes();
			if (is_array($this->post($cat->getAttributeKeyCategoryHandle()))) {
				foreach($this->post($cat->getAttributeKeyCategoryHandle()) as $id) {
					$type = AttributeType::getByID($id);
					$cat->associateAttributeKeyType($type);
				}
			}
		}
		
		$this->redirect('dashboard/settings', 'manage_attribute_types', 'associations_updated');
	}

	public function manage_attribute_types($mode = false) {
				
		if ($mode != false) {
			switch($mode) {
				case 'associations_updated':
					$this->set('message', 'Attribute Types saved.');
					break;
				case 'attribute_type_added':
					$this->set('message', 'Attribute Type added.');
					break;
			}
		}
	}
	
	public function on_start() {
		$prefsSelected = false;
		$globalSelected = false;
		$permsSelected = false;
		$this->token = Loader::helper('validation/token');
		
		switch($this->getTask()) {
			case 'manage_attribute_types':
				$attrSelected = true;
				break;
			case "set_developer":
			case "export_database_schema":
			case "refresh_database_schema":
				$devSelected = true;
				break;
			case "access_task_permissions":
			case "set_permissions":
				$permsSelected = true;
				break;
			case 'view':
				$globalSelected = true;
				break;
		}					
		$subnav = array(
			array(View::url('/dashboard/settings'), t('General'), $globalSelected),
			array(View::url('/dashboard/settings/mail'), t('Email')),
			array(View::url('/dashboard/settings', 'set_permissions'), t('Access'), $permsSelected),
			array(View::url('/dashboard/settings', 'set_developer'), t('Debug'), $devSelected),
			array(View::url('/dashboard/settings', 'manage_attribute_types'), t('Attributes'), $attrSelected)
		);
		$this->set('subnav', $subnav);
	}
	
	protected function set_permissions($saved = false) {
		//IP Address Blacklist
		Loader::model('user_banned_ip');
		$ip_ban_enable_lock_ip_after 	= Config::get('IP_BAN_LOCK_IP_ENABLE');
		$ip_ban_enable_lock_ip_after	= ($ip_ban_enable_lock_ip_after == 1) ? 1 : 0;
		$ip_ban_lock_ip_after_attempts 	= Config::get('IP_BAN_LOCK_IP_ATTEMPTS');
		$ip_ban_lock_ip_after_time		= Config::get('IP_BAN_LOCK_IP_TIME');		
		$ip_ban_lock_ip_how_long_min	= Config::get('IP_BAN_LOCK_IP_HOW_LONG_MIN') ? Config::get('IP_BAN_LOCK_IP_HOW_LONG_MIN') : '';
		if(!$ip_ban_lock_ip_how_long_min){
			$ip_ban_lock_ip_how_long_type = self::IP_BAN_LOCK_IP_HOW_LONG_TYPE_FOREVER;
		}
		else{
			$ip_ban_lock_ip_how_long_type = self::IP_BAN_LOCK_IP_HOW_LONG_TYPE_TIMED;		
		}
		
		$user_banned_ip 				= new UserBannedIP();	
		//pull all once filter various lists using code
		$user_banned_ips 				= $user_banned_ip->Find('1=1');		
		$user_banned_manual_ips 		= Array();
		$user_banned_limited_ips 		= Array();
		
		foreach ($user_banned_ips as $user_banned_ip) { 				
			if ($user_banned_ip->isManual == 1) {	
				$user_banned_manual_ips[] = $user_banned_ip->getIPRangeForDisplay();								
			}
			else if ($user_banned_ip->expires - time() > 0 || $user_banned_ip->expires == 0) {
				$user_banned_limited_ips[] =  $user_banned_ip;
			}
		}
		$user_banned_manual_ips = join($user_banned_manual_ips,"\n");
		$this->set('user_banned_manual_ips',$user_banned_manual_ips);
		$this->set('user_banned_limited_ips',$user_banned_limited_ips);
		$this->set('ip_ban_enable_lock_ip_after',$ip_ban_enable_lock_ip_after);
		$this->set('ip_ban_lock_ip_after_attempts',$ip_ban_lock_ip_after_attempts);
		$this->set('ip_ban_lock_ip_after_time',$ip_ban_lock_ip_after_time);
		$this->set('ip_ban_change_makeperm',self::IP_BLACKLIST_CHANGE_MAKEPERM);
		$this->set('ip_ban_change_remove',self::IP_BLACKLIST_CHANGE_REMOVE);		
		
		$this->set('ip_ban_lock_ip_how_long_type',$ip_ban_lock_ip_how_long_type);
		$this->set('ip_ban_lock_ip_how_long_type',$ip_ban_lock_ip_how_long_type);
		$this->set('ip_ban_lock_ip_how_long_type_forever',self::IP_BAN_LOCK_IP_HOW_LONG_TYPE_FOREVER);
		$this->set('ip_ban_lock_ip_how_long_type_timed',self::IP_BAN_LOCK_IP_HOW_LONG_TYPE_TIMED);		
		$this->set('ip_ban_lock_ip_how_long_min',$ip_ban_lock_ip_how_long_min);
	
		
		//maintanence mode
		$site_maintenance_mode = Config::get('SITE_MAINTENANCE_MODE');
		if ($site_maintenance_mode < 1) {
			$site_maintenance_mode = 0;
		}
		$this->set('site_maintenance_mode', $site_maintenance_mode);	
		$this->set('user_banned_ips',$user_banned_ips);
		
		if ($saved) {
			switch($saved) { 
				case "maintenance_enabled";
					$this->set('message', t('Maintenance Mode turned on. Your site is now private.'));	
					break;
				case "maintenance_disabled":
					$this->set('message', t('Maintenance Mode turned off. Your site is public.'));	
					break;	
				case "saved_ipblacklist":
					$this->set('message',t('IP Blacklist Settings Updated'));
					break; 								
				//permissions saved	
				default: 
					$this->set('message', t('Permissions saved.'));	
			}
		}	
	
		if (PERMISSIONS_MODEL != 'simple') {
			return;
		}
		
		$home = Page::getByID(1, "RECENT");
		$gl = new GroupList($home, false, true);
		$gArrayTmp = $gl->getGroupList();
		$gArray = array();
		foreach($gArrayTmp as $gi) {
			if ($gi->getGroupID() == GUEST_GROUP_ID) {
				$ggu = $gi;
				if ($ggu->canRead()) {
					$this->set('guestCanRead', true);
				}
			} else if ($gi->getGroupID() == REGISTERED_GROUP_ID) {
				$gru = $gi;
				if ($gru->canRead()) {
					$this->set('registeredCanRead', true);
				}
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
		$this->set_permissions();
		$home = $this->get('home');
		$gru = Group::getByID(REGISTERED_GROUP_ID);
		$ggu = Group::getByID(GUEST_GROUP_ID);
		$gau = Group::getByID(ADMIN_GROUP_ID);
		
		$args = array();
		switch($_POST['view']) {
			case "ANYONE":
				$args['collectionRead'][] = 'gID:' . $ggu->getGroupID(); // this API is pretty crappy. TODO: clean this up in a nice object oriented fashion
				break;
			case "USERS":
				$args['collectionRead'][] = 'gID:' . $gru->getGroupID(); // this API is pretty crappy. TODO: clean this up in a nice object oriented fashion
				break;
			case "PRIVATE":
				$args['collectionRead'][] = 'gID:' . $gau->getGroupID();
				break;
			
		}
		
		$args['collectionWrite'] = array();
		if (is_array($_POST['gID'])) {
			foreach($_POST['gID'] as $gID) {
				$args['collectionReadVersions'][] = 'gID:' . $gID;
				$args['collectionWrite'][] = 'gID:' . $gID;
				$args['collectionAdmin'][] = 'gID:' . $gID;
				$args['collectionDelete'][] = 'gID:' . $gID;
			}
		}
		
		$args['cInheritPermissionsFrom'] = 'OVERRIDE';
		$args['cOverrideTemplatePermissions'] = 1;
		
		$home->updatePermissions($args);
		
		$this->redirect('/dashboard/settings/', 'set_permissions', 'permissions_saved');
	}
	
	function update_favicon(){
		Loader::library('file/importer');
		if ($this->token->validate("update_favicon")) { 
		
			if(intval($this->post('remove_favicon'))==1){
				Config::save('FAVICON_FID',0);
				$this->redirect('/dashboard/settings/', 'favicon_removed');
			} else {
				$fi = new FileImporter();
				$resp = $fi->import($_FILES['favicon_file']['tmp_name'], $_FILES['favicon_file']['name'], $fr);
	
				if (!($resp instanceof FileVersion)) {
					switch($resp) {
						case FileImporter::E_FILE_INVALID_EXTENSION:
							$this->set('error', array(t('Invalid file extension.')));
							break;
						case FileImporter::E_FILE_INVALID:
							$this->set('error', array(t('Invalid file.')));
							break;
						
					}
				} else {
				
					Config::save('FAVICON_FID', $resp->getFileID());
					$filepath=$resp->getPath();  
					//@copy($filepath, DIR_BASE.'/favicon.ico');
					$this->redirect('/dashboard/settings/', 'favicon_saved');

				}
			}		
			
		}else{
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}
	
	function update_marketplace_support(){ 		
		if ($this->token->validate("update_marketplace_support")) { 
 			Config::save('ENABLE_MARKETPLACE_SUPPORT', intval($this->post('MARKETPLACE_ENABLED')) );
 			if($this->post('MARKETPLACE_ENABLED'))
 				 $this->redirect('/dashboard/settings/', 'marketplace_turned_on');
			else $this->redirect('/dashboard/settings/', 'marketplace_turned_off');	
		}else{
			$this->set('error', array($this->token->getErrorMessage()));
		} 
	}
	
	function txt_editor_config(){
		if ($this->token->validate("txt_editor_config")) { 
 			Config::save('CONTENTS_TXT_EDITOR_MODE', $this->post('CONTENTS_TXT_EDITOR_MODE') );
			
			$textEditorWidth = intval($this->post('CONTENTS_TXT_EDITOR_WIDTH'));
			if( $textEditorWidth<580 ) $textEditorWidth=580;
			Config::save( 'CONTENTS_TXT_EDITOR_WIDTH', $textEditorWidth );
			
			$textEditorHeight = intval($this->post('CONTENTS_TXT_EDITOR_HEIGHT'));
			if( $textEditorHeight<100 ) $textEditorHeight=380;
			Config::save( 'CONTENTS_TXT_EDITOR_HEIGHT', $textEditorHeight );	
			
			$db = Loader::db();
			$values=array( $textEditorWidth, $textEditorHeight );
			$db->query( 'UPDATE BlockTypes SET btInterfaceWidth=?, btInterfaceHeight=? where btHandle = "content"', $values );
			
			if($this->post('CONTENTS_TXT_EDITOR_MODE')=='CUSTOM')
				Config::save('CONTENTS_TXT_EDITOR_CUSTOM_CODE', $this->post('CONTENTS_TXT_EDITOR_CUSTOM_CODE') );
 			$this->redirect('/dashboard/settings/', 'txt_editor_config_saved'); 
		}else{
			$this->set('error', array($this->token->getErrorMessage()));
		}
	}
	
	function get_txt_editor_default(){ 
		ob_start();
		?>
theme : "concrete", 
plugins: "inlinepopups,spellchecker,safari,advlink",
editor_selector : "ccm-advanced-editor",
spellchecker_languages : "+English=en",	
theme_concrete_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,hr,|,styleselect,formatselect,fontsizeselect",
theme_concrete_buttons2 : "bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,forecolor",
theme_concrete_blockformats : "p,address,pre,h1,h2,h3,div,blockquote,cite",
theme_concrete_toolbar_align : "left",
theme_concrete_fonts : "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats",
theme_concrete_font_sizes : "1,2,3,4,5,6,7",
theme_concrete_styles: "Note=ccm-note",
spellchecker_languages : "+English=en"

/*
// Use the advanced theme for more than two rows of content
plugins: "inlinepopups,spellchecker,safari,advlink,table,advhr,xhtmlxtras,emotions,insertdatetime,paste,visualchars,nonbreaking,pagebreak,style",
editor_selector : "ccm-advanced-editor",
theme : "advanced",
theme_advanced_buttons1 : "cut,copy,paste,pastetext,pasteword,|,undo,redo,|,styleselect,formatselect,fontsizeselect,fontselect",
theme_advanced_buttons2 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,anchor,|,forecolor,backcolor,|,image,charmap,emotions",
theme_advanced_fonts : "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats",
// etc.
*/		
		<?php  
		$js=ob_get_contents();
		ob_end_clean();
		return $js;
	}
}