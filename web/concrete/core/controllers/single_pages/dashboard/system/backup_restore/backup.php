<?php 
defined('C5_EXECUTE') or die("Access Denied.");
Loader::library('backup');
class Concrete5_Controller_Dashboard_System_BackupRestore_Backup extends DashboardBaseController { 	 

   public function on_start() {
      $this->addHeaderItem(Loader::helper('html')->javascript('jquery.cookie.js'));
	 parent::on_start();
   } 


	public function run_backup() {
	  $encrypt = $this->post('useEncryption');
	  $tp = new TaskPermission();
  	  if ($tp->canBackup()) {		
          $encrypt = (bool) $encrypt;
          try {
	          $backup = Backup::execute($encrypt);   
 			} catch(Exception $e) {
 				$this->set('error', $e);
 			}
 			$this->view();
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
				preg_match('/[0-9]+/',$bkupfile,$timestamp);
				$arr_backupfileinfo[] = Array("file" => $bkupfile,  "date" =>  date("Y-m-d H:i:s",$timestamp[0]));
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
			$this->set('error', array(t('Unable to locate file %s', DIR_FILES_BACKUPS . '/' . $file)));
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
	  if (!is_null($str_fname) && trim($str_fname) != "" && !preg_match('/\.\./',$str_fname) && file_exists(DIR_FILES_BACKUPS . "/$str_fname")) {
		 chmod(DIR_FILES_BACKUPS . "/$str_fname",666);
		 unlink(DIR_FILES_BACKUPS . "/$str_fname");
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
		//$str_restSql = file_get_contents(DIR_FILES_BACKUPS . '/' . $file);
		if (!$str_restSql) {
			$this->set("error",array("There was an error trying to restore the database. This file was empty."));
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
					$this->set("error",array("There was an error trying to restore the database. In query $str_stmt"));
					return;
				}
			}		
		}
		
		$this->set("message","Restoration Sucessful");
	
		//reset perms for security! 
		chmod(DIR_FILES_BACKUPS . '/'. $file, 000);
		Cache::flush();
		$this->view();
	}
	  
}