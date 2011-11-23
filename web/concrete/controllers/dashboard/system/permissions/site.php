<?php defined('C5_EXECUTE') or die("Access Denied.");
class DashboardSystemPermissionsSiteController extends DashboardBaseController {
	public function view() {
		if (PERMISSIONS_MODEL != 'simple') {
			return;
		}
		
		$home = Page::getByID(1, "RECENT");
		$gl = new GroupList($home, false, true);
		$gArrayTmp = $gl->getGroupList();
		$gArray = array();
		foreach($gArrayTmp as $gi) {
			if ($gi->getGroupID() == GUEST_GROUP_ID) {
				$ggu = $gi;
				if ($ggu->canRead()) {
					$this->set('guestCanRead', true);
				}
			} else if ($gi->getGroupID() == REGISTERED_GROUP_ID) {
				$gru = $gi;
				if ($gru->canRead()) {
					$this->set('registeredCanRead', true);
				}
			} else {
				$gArray[] = $gi;
			}
		}
		
		$this->set('ggu', $ggu);
		$this->set('gru', $gru);
		$this->set('gArray', $gArray);
		$this->set('home', $home);
		
		if ($this->isPost()) {
			if ($this->token->validate('site_permissions_code')) {
				$gru = Group::getByID(REGISTERED_GROUP_ID);
				$ggu = Group::getByID(GUEST_GROUP_ID);
				$gau = Group::getByID(ADMIN_GROUP_ID);
				$args = array();
				switch($_POST['view']) {
					case "ANYONE":
						$args['collectionRead'][] = 'gID:' . $ggu->getGroupID(); // this API is pretty crappy. TODO: clean this up in a nice object oriented fashion
						break;
					case "USERS":
						$args['collectionRead'][] = 'gID:' . $gru->getGroupID(); // this API is pretty crappy. TODO: clean this up in a nice object oriented fashion
						break;
					case "PRIVATE":
						$args['collectionRead'][] = 'gID:' . $gau->getGroupID();
						break;
							
				}
				
				$args['collectionWrite'] = array();
				if (is_array($_POST['gID'])) {
					foreach($_POST['gID'] as $gID) {
						$args['collectionReadVersions'][] = 'gID:' . $gID;
						$args['collectionWrite'][] = 'gID:' . $gID;
						$args['collectionAdmin'][] = 'gID:' . $gID;
						$args['collectionDelete'][] = 'gID:' . $gID;
					}
				}
				
				$args['cInheritPermissionsFrom'] = 'OVERRIDE';
				$args['cOverrideTemplatePermissions'] = 1;
				
				$home->updatePermissions($args);
				
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