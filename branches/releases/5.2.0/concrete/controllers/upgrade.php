<?php 
defined('C5_EXECUTE') or die(_("Access Denied."));
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);
class UpgradeController extends Controller {

	private $notes = array();
	private $upgrades = array();
	private $site_version = null;
	
	public function on_start() {
		$this->site_version = Config::get('SITE_APP_VERSION');
	}
	
	public function view() {
		
		$sav = $this->site_version;

		if (!$sav) {
			$message = t('Unable to determine your current version of Concrete. Upgrading cannot continue.');
		} else 	if (version_compare($sav, APP_VERSION, '>')) {
			$message = t('Upgrading from <b>%s</b>', $sav) . '<br/>';
			$message .= t('Upgrading to <b>%s</b>', APP_VERSION) . '<br/><br/>';
			$message .= t('Your current website uses a version of Concrete5 greater than this one. You cannot upgrade.');
			
			$this->set('message', $message);
		} else if (version_compare($sav, APP_VERSION, '=')) {
			$this->set('message', t('Your site is already up to date! The current version of Concrete5 is <b>%s</b>. You should remove this file for security.', APP_VERSION));
		} else {
			
			if ($this->post('do_upgrade')) {
				$this->do_upgrade();
			} else {
				
				// do the upgrade
				$this->set_upgrades();
				$allnotes = array();
				foreach($this->upgrades as $ugh) {
					if (method_exists($ugh, 'notes')) {
						$notes = $ugh->notes();
						if ($notes != '') {
							if (is_array($notes)) {
								$allnotes = array_merge($allnotes, $notes);
							} else {
								$allnotes[] = $notes;
							}
						}
					}
				}
				
				$message = '';
				$message = t('Upgrading from <b>%s</b>', $sav) . '<br/>';
				$message .= t('Upgrading to <b>%s</b>', APP_VERSION) . '<br/><br/>';

				if (count($allnotes) > 0) { 
					$message .= '<ul>';
					foreach($allnotes as $n) {
						$message .= '<li>' . $n . '</li>';
					}
					$message .= '</ul><br/>';
				}
				
				$this->set('do_upgrade', true);			
				$this->set('message', $message);
			}
		}
		
	}
	
	private function set_upgrades() {
		$ugvs = array();
		
		/*
		The upgrades file behave as follows:
		for a specific site_version, if special crap is to happen, it gets a version_xxx file in the
		helpers directory. For example, let's say a Concrete5 b1 site wants to upgrade to b4, the current version
		Say that b2 introduces some new items, b3 introduces no new pages/db changes, and b4 does
		then we'd have
		case "5.0.0b1":
			$ugvs[] = "version_500b2";
			$ugvs[] = "version_500b4";
			break;
		case "5.0.0b2":
			$ugvs[] = "version_500b4";
			break;
		case "5.0.0b3":
			$ugvs[] = "version_500b4";
			break;
			
		This typically shouldn't include schema changes which will be picked up by refresh_schema
		*/

		switch(strtolower($this->site_version)) {
			case "5.0.0a1":
				$ugvs[] = "version_500a1";
				$ugvs[] = "version_500b1";
				$ugvs[] = "version_500b2";
				$ugvs[] = "version_500";
				$ugvs[] = "version_510";
				break;
			case "5.0.0b1":
				$ugvs[] = "version_500b1";
				$ugvs[] = "version_500b2";
				$ugvs[] = "version_500";
				$ugvs[] = "version_510";
				break;
			case "5.0.0b2":
				$ugvs[] = "version_500b2";
				$ugvs[] = "version_500";
				$ugvs[] = "version_510";
				break;
			case "5.0.0":
				$ugvs[] = "version_500";
				$ugvs[] = "version_510";
				break;
			case "5.1.0rc1":
			case "5.1.0rc2":
			case "5.1.0":
			case "5.1.1":
				$ugvs[] = "version_510";
				break;
		}
		
		foreach($ugvs as $ugh) {
			$this->upgrades[] = Loader::helper('concrete/upgrade/' . $ugh);
		}
	}
	
	private function refresh_schema() {
		$installDirectory = DIR_BASE_CORE . '/config';
		$file = $installDirectory . '/db.xml';
		if (!file_exists($file)) {
			throw new Exception(t('Unable to locate database import file.'));
		}		
		$err = Package::installDB($file);
		
		// now we refresh the block schema
		$btl = new BlockTypeList();
		$btArray = $btl->getInstalledList();
		foreach($btArray as $bt) {
			$path = $bt->getBlockTypePath();
			if (file_exists($path . '/' . FILENAME_BLOCK_DB)) {
				Package::installDB($path . '/' . FILENAME_BLOCK_DB);
			}
		}
	}
	
	private function do_upgrade() {
		try {
			$this->set_upgrades();
			$this->refresh_schema();
			$ca = new Cache();
			$ca->flush();
			foreach($this->upgrades as $ugh) {
				$ugh->run();
			}

			$upgrade = true;
		} catch(Exception $e) {
			$upgrade = false;
			$message = t('Error occurred while upgrading: %s', $e->getMessage());
		}
		
		if ($upgrade) { 
			$message .= t('Upgrade to <b>%s</b> complete!', APP_VERSION) . '<br/><br/>';
			Config::save('SITE_APP_VERSION', APP_VERSION);
		}
		
		$this->set('message', $message);

	}
	
}
	
