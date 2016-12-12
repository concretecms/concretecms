<?php
namespace Concrete\Core\Legacy;
use FileSet;
use Permissions;

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
