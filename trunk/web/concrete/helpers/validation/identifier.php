<?
defined('C5_EXECUTE') or die(_("Access Denied."));
class ValidationIdentifierHelper {

	private $letters = 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
	
	/**
	 * Generates a unique identifier for an item in a database table. Used, among other places, in generating
	 * User hashes for email validation
	 * @param string table
	 * @param string key
	 * @param int length
	 */
	public function generate($table, $key, $length = 12) {
		$foundHash = false;
		$db = Loader::db();
		while ($foundHash == false) {
			$string = $this->getString($length);
			$cnt = $db->GetOne("select count(" . $key . ") as total from " . $table . " where " . $key . " = ?", array($string));
			if ($cnt < 1) {
				$foundHash = true;
			}
		}
		return $string;
	}
	
	public function getString($length = 12) {
		$hash = substr(str_shuffle($this->letters), 0, $length);
		return $hash;
	}

}