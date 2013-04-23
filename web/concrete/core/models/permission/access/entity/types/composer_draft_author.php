<?
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Model_ComposerDraftAuthorPermissionAccessEntity extends PermissionAccessEntity {

	public function getAccessEntityUsers(PermissionAccess $pae) {
		$d = $pae->getPermissionObject();
		if (is_object($d) && $d instanceof ComposerDraft) {
			$ui = UserInfo::getByID($d->getComposerDraftUserID());
			$users = array($ui);
			return $users;
		}
	}

	public function validate(PermissionAccess $pae) {
		$users = $this->getAccessEntityUsers($pae);
		if (count($users) == 0) {
			return false;
		} else if (is_object($users[0])) {
			$u = new User();
			return $users[0]->getUserID() == $u->getUserID();
		}
	}
	
	public function getAccessEntityTypeLinkHTML() {
		$html = '<a href="javascript:void(0)" onclick="ccm_choosePermissionAccessEntityComposerDraftAuthor()">' . t('Draft Author') . '</a>';
		return $html;		
	}

	public static function getAccessEntitiesForUser($user) {
		$entities = array();
		$db = Loader::db();
		if ($user->isRegistered()) { 
			$pae = ComposerDraftAuthorPermissionAccessEntity::getOrCreate();
			$r = $db->GetOne('select cmpDraftID from ComposerDrafts where uID = ?', array($user->getUserID()));
			if ($r > 0) {
				$entities[] = $pae;
			}
		}
		return $entities;		
	}
	
	public static function getOrCreate() {
		$db = Loader::db();
		$petID = $db->GetOne('select petID from PermissionAccessEntityTypes where petHandle = \'composer_draft_author\'');
		$peID = $db->GetOne('select peID from PermissionAccessEntities where petID = ?', 
			array($petID));
		if (!$peID) { 
			$db->Execute("insert into PermissionAccessEntities (petID) values(?)", array($petID));
			Config::save('ACCESS_ENTITY_UPDATED', time());
			$peID = $db->Insert_ID();
		}
		return PermissionAccessEntity::getByID($peID);
	}

	public function load() {
		$db = Loader::db();
		$this->label = t('Draft Author');
	}

}