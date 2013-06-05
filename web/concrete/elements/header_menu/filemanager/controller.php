<?
defined('C5_EXECUTE') or die("Access Denied.");
class FilemanagerConcreteInterfaceMenuItemController extends ConcreteInterfaceMenuItemController {
	
	public function displayItem() {
		$u = new User();
		if ($u->isRegistered()) {
			$fp = FilePermissions::getGlobal();
			if ($fp->canSearchFiles() && $u->config('UI_FILEMANAGER')) {
				return true;
			}
		}
		return false;
	}

}