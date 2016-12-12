<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Backup;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Core;
use Loader;
use TaskPermission;
use Exception;
use \Concrete\Core\Backup\Backup as ConcreteBackup;

class Backup extends DashboardPageController {

	public function run_backup() {
        if($this->token->validate('run_backup')) {

            //@TODO this backup stuff needs to be reworked since we're not using adodb anymore
            $this->set('message', t('This has not been implemented in 5.7'));
            return;

            $encrypt = ($this->post('useEncryption')?1:0);
            $tp = new TaskPermission();
            if ($tp->canBackup()) {
                try {
                    ConcreteBackup::execute($encrypt);
                } catch(Exception $e) {
                    $this->error->add($e);
                }
                $this->view();
            }

        } else {
            $this->set('error', array($this->token->getErrorMessage()));
        }
	}

	public function view() {
		$tp = new TaskPermission();
		if ($tp->canBackup()) {
			$fh = Loader::helper('file');
			$arr_bckups = @$fh->getDirectoryContents(DIR_FILES_BACKUPS);
			$arr_backupfileinfo = Array();
			if (count($arr_bckups) > 0) {
			 foreach ($arr_bckups as $bkupfile) {
			 	// This will ignore files that do not match the created backup pattern of including a timestamp in the filename
				if (preg_match("/_([\d]{10,})/", $bkupfile, $timestamp)){
					$arr_backupfileinfo[] = Array("file" => $bkupfile,  "date" =>  date("Y-m-d H:i:s",$timestamp[1]));
				}
			 }
			 $this->set('backups',$arr_backupfileinfo);
			}

		}
	}

	public function download($file) {
		$tp = new TaskPermission();
		  if (!$tp->canBackup()) {
			return false;
		}

		if (file_exists(DIR_FILES_BACKUPS . '/'. $file)) {
			chmod(DIR_FILES_BACKUPS . '/'. $file, 0666);
			if (file_exists(DIR_FILES_BACKUPS . '/' . $file)) {
				$f = Loader::helper('file');
				$f->forceDownload(DIR_FILES_BACKUPS . '/' . $file);
				exit;
			}
			chmod(DIR_FILES_BACKUPS . '/'. $file, 000);
		} else {
			$this->error->add(t('Unable to locate file %s', DIR_FILES_BACKUPS . '/' . $file));
			$this->view();
		}
	}

	public function delete_backup() {
		$tp = new TaskPermission();
		  if (!$tp->canBackup()) {
			return false;
		}
		$str_fname = $this->post('backup_file');
	  //For Security reasons...  allow only known characters in the string e.g no / \ so you can't exploit this
	  $int_mResult = preg_match('/[0-9A-Za-z._]+/',$str_fname,$ar_matches);
	  $str_fname = $ar_matches[0];
		if (!is_null($str_fname) && trim($str_fname) != "" && !preg_match('/\.\./',$str_fname)) {
			$fullFilename = DIR_FILES_BACKUPS . "/$str_fname";
			if(is_file($fullFilename)) {
				@chmod($fullFilename, 666);
				@unlink($fullFilename);
				if(is_file($fullFilename)) {
					$this->error->add(t('Error deleting the file %s. Please check the permissions of the folder %s', $str_fname, DIR_FILES_BACKUPS));
				}
			}
		}
		$this->view();
	}

	public function restore_backup() {
		set_time_limit(0);
		$tp = new TaskPermission();
		  if (!$tp->canBackup()) {
			return false;
		}

		$file = basename(realpath(DIR_FILES_BACKUPS . '/' . $this->post('backup_file')));
		$fh = Loader::helper('file');

		$db = Loader::db();
		if (!file_exists(DIR_FILES_BACKUPS . '/'. $file)) {
			throw new Exception(t('Invalid backup file specified.'));
		}
		chmod(DIR_FILES_BACKUPS . '/'. $file, 0666);
		$str_restSql = $fh->getContents(DIR_FILES_BACKUPS . '/' . $file);
		if (!$str_restSql) {
			$this->error->add(t("There was an error trying to restore the database. This file was empty."));
			$this->view();
			return false;
		}
		$crypt = Loader::helper('encryption');
		if ( !preg_match('/INSERT/m',$str_restSql) && !preg_match('/CREATE/m',$str_restSql) ) {
			$str_restSql = $crypt->decrypt($str_restSql);
		}
		$arr_sqlStmts = explode("\n\n",$str_restSql);

		foreach ($arr_sqlStmts as $str_stmt) {
			if (trim($str_stmt) != "") {
				$res_restoration = $db->execute($str_stmt);
				if (!$res_restoration) {
					$this->error->add(t("There was an error trying to restore the database. Affected query: %s", $str_stmt));
					$this->view();
					return false;
				}
			}
		}

		//reset perms for security!
		chmod(DIR_FILES_BACKUPS . '/'. $file, 000);

        $cms = Core::make('app');
        $cms->clearCaches();

		$this->redirect('/dashboard/system/backup/backup', 'restoration_successful');
	}

	public function restoration_successful() {
		$this->set('message', t('Restoration Successful'));
		$this->view();
	}

}
