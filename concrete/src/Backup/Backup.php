<?php
namespace Concrete\Core\Backup;
use Loader;
class Backup {

	public function execute($encrypt = false) {
		$db = Loader::db();
		if (!file_exists(DIR_FILES_BACKUPS)) {
			mkdir(DIR_FILES_BACKUPS);
			file_put_contents(DIR_FILES_BACKUPS . "/.htaccess","Order Deny,Allow\nDeny from all");
		}
		$str_bkupfile = "dbu_". time() .".sql";
		$arr_tables = $db->getCol("SHOW TABLES FROM `" . DB_DATABASE . "`");
		foreach ($arr_tables as $bkuptable) {
			$tableobj = new BackupTable($bkuptable);
			$str_backupdata .= "DROP TABLE IF EXISTS $bkuptable;\n\n";
			$str_backupdata .= $tableobj->str_createTableSql . "\n\n";
			if ($tableobj->str_createTableSql != "" ) {
				$str_backupdata .= $tableobj->str_insertionSql . "\n\n";
			}
		}
		$fh_backupfile = @fopen(DIR_FILES_BACKUPS . "/". $str_bkupfile,"w");
		if (!$fh_backupfile) {
			throw new Exception(t('Unable to create backup file: %s', $str_bkupfile));
		}
		if ($encrypt == true) {
			$crypt = Loader::helper('encryption');
			fwrite($fh_backupfile,$crypt->encrypt($str_backupdata));
		} else  {
			fwrite($fh_backupfile,$str_backupdata);
		}
		fclose($fh_backupfile);
		//for security
		chmod(DIR_FILES_BACKUPS . "/". $str_bkupfile,000);
	}

}
