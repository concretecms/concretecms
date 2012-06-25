<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_BackupRestore_Database extends DashboardBaseController {
	
	public function view(){
	}
	
	public function export_database_schema() {
		if (!ENABLE_DEVELOPER_OPTIONS) { 
			return false;
		}
		$db = Loader::db();
		$ab = Database::getADOSChema();
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
}