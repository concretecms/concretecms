<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Number {

	public function flexround($value) {
		$v = explode('.', $value);
		$p = 0;
		for ($i = 0; $i < strlen($v[1]); $i++) {
			if (substr($v[1], $i, 1) > 0) {
				$p = $i+1;
			}
		}
		return round($value, $p);
	}
}

?>