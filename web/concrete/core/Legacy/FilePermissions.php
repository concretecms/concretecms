<?
/**
 * @deprecated
 */

final class FilePermissions {

	public static function getGlobal() {
		$fs = FileSet::getGlobal();
		$fsp = new Permissions($fs);
		return $fsp;
	}
}