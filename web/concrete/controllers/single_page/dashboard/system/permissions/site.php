<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Permissions;
use \Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Core;
use Loader;
use PermissionKey;
use TaskPermission;
use PermissionAccess;
use GroupList;
use Page;
use Group;
use \Concrete\Core\Permission\Access\Entity\GroupEntity as GroupPermissionAccessEntity;

class Site extends DashboardPageController {
	public function view() {
		$editAccess = array();
		if (Config::get('concrete.permissions.model') != 'simple') {
			return;
		}


		$home = Page::getByID(1, "RECENT");
		$pk = PermissionKey::getByHandle('view_page');
		$pk->setPermissionObject($home);
		$assignments = $pk->getAccessListItems();
		foreach($assignments as $asi) {
			$ae = $asi->getAccessEntityObject();
			if ($ae->getAccessEntityTypeHandle() == 'group' && $ae->getGroupObject()->getGroupID() == GUEST_GROUP_ID) {
				$this->set('guestCanRead', true);
			} else if ($ae->getAccessEntityTypeHandle() == 'group' && $ae->getGroupObject()->getGroupID() == REGISTERED_GROUP_ID) {
				$this->set('registeredCanRead', true);
			}
		}

		$gl = new GroupList();
		$gl->filter('gID', REGISTERED_GROUP_ID, '>');
		$gIDs = $gl->getResults();
		$gArray = array();
		foreach($gIDs as $g) {
			$gArray[] = $g;
		}

		$pk = PermissionKey::getByHandle('edit_page_contents');
		$pk->setPermissionObject($home);
		$assignments = $pk->getAccessListItems();
		foreach($assignments as $asi) {
			$ae = $asi->getAccessEntityObject();
			if ($ae->getAccessEntityTypeHandle() == 'group') {
				$groupObject = $ae->getGroupObject();
				if ($groupObject){
				   $editAccess[] = $ae->getGroupObject()->getGroupID();
				}
			}
		}

		$this->set('home', $home);
		$this->set('gArray', $gArray);
		$this->set('editAccess', $editAccess);

		if ($this->isPost()) {
			if ($this->token->validate('site_permissions_code')) {

				switch($_POST['view']) {
					case "ANYONE":
						$viewObj = GroupPermissionAccessEntity::getOrCreate(Group::getByID(GUEST_GROUP_ID));
						break;
					case "USERS":
						$viewObj = GroupPermissionAccessEntity::getOrCreate(Group::getByID(REGISTERED_GROUP_ID));
						break;
					case "PRIVATE":
						$viewObj = GroupPermissionAccessEntity::getOrCreate(Group::getByID(ADMIN_GROUP_ID));
						break;
				}


				$pk = PermissionKey::getByHandle('view_page');
				$pk->setPermissionObject($home);
				$pt = $pk->getPermissionAssignmentObject();
				$pt->clearPermissionAssignment();
				$pa = PermissionAccess::create($pk);
				$pa->addListItem($viewObj);
				$pt->assignPermissionAccess($pa);

				$editAccessEntities = array();
				if (is_array($_POST['gID'])) {
					foreach($_POST['gID'] as $gID) {
						$editAccessEntities[] = GroupPermissionAccessEntity::getOrCreate(Group::getByID($gID));
					}
				}

				$editPermissions = array(
					'view_page_versions',
					'edit_page_properties',
					'edit_page_contents',
					'edit_page_speed_settings',
					'edit_page_multilingual_settings',
					'edit_page_theme',
					'edit_page_page_type',
					'edit_page_template',
					'edit_page_permissions',
					'delete_page',
					'preview_page_as_user',
					'schedule_page_contents_guest_access',
					'delete_page_versions',
					'approve_page_versions',
					'add_subpage',
					'move_or_copy_page',
				);
				foreach($editPermissions as $pkHandle) {
					$pk = PermissionKey::getByHandle($pkHandle);
					$pk->setPermissionObject($home);
					$pt = $pk->getPermissionAssignmentObject();
					$pt->clearPermissionAssignment();
					$pa = PermissionAccess::create($pk);
					foreach($editAccessEntities as $editObj) {
						$pa->addListItem($editObj);
					}
					$pt->assignPermissionAccess($pa);
				}
				$pkx = PermissionKey::getbyHandle('add_block');
				$pt = $pkx->getPermissionAssignmentObject();
				$pt->clearPermissionAssignment();
				$pa = PermissionAccess::create($pkx);
				foreach($editAccessEntities as $editObj) {
					$pa->addListItem($editObj);
				}
				$pt->assignPermissionAccess($pa);

				$pkx = PermissionKey::getbyHandle('add_stack');
				$pt = $pkx->getPermissionAssignmentObject();
				$pt->clearPermissionAssignment();
				$pa = PermissionAccess::create($pkx);
				foreach($editAccessEntities as $editObj) {
					$pa->addListItem($editObj);
				}
				$pt->assignPermissionAccess($pa);

                $cms = Core::make('app');
                $cms->clearCaches();
				$this->redirect('/dashboard/system/permissions/site/', 'saved');
			} else {
				$this->error->add($this->token->getErrorMessage());
			}
		}
	}

	public function saved() {
		$this->view();
		$this->set('message', t('Permissions saved'));
	}
}