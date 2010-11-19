<?php 
 
defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteDashboardTaskPermissionsHelper { 
	
	public function save($post) {
		// clear all selected permissions
		$tps = array();
		foreach($post['tpID'] as $tpID) {
			$tp = TaskPermission::getByID($tpID);
			$tps[] = $tp;
			$tp->clearPermissions();
		}
		foreach($post['selectedEntity'] as $e) {
			if ($e != '') {
				$o1 = explode('_', $e);
				if ($o1[0] == 'uID') {
					$obj = UserInfo::getByID($o1[1]);
				} else {
					$obj = Group::getByID($o1[1]);					
				}
				
				foreach($tps as $tp) {
					if ($post[$e . '_' . $tp->getTaskPermissionID()] == 1) {
						$tp->addAccess($obj);
					}
				}
			}
		}	
	}

	public function getForm($item, $description = '') {
		
		if ($item instanceof TaskPermission) {
			$tp = new TaskPermissionList();
			$tp->add($item);
		} else {
			$tp = $item;
		}

		$gl = new GroupList($tp);
		$ul = new UserInfoList($tp);
		$uArray = $ul->getUserInfoList();
		$gArray = $gl->getGroupList();
		
		$tps = $tp->getTaskPermissions();
		$html = '';
		foreach($tps as $_tp) {
			$html .= '<input type="hidden" name="tpID[]" value="' . $_tp->getTaskPermissionID() . '" />';
		}
		
		$html .= '<a href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/user_group_selector" id="ug-selector" dialog-modal="false" dialog-width="90%" dialog-title="' . t('Choose User/Group') . '"  dialog-height="70%" class="ccm-button-right dialog-launch"><span><em>' . t('Add Group or User') . '</em></span></a>';
		$html .= '<p>' . $description . '</p>';

		$html .= '<div class="ccm-spacer">&nbsp;</div><br/>';		
		$html .= '<div id="ccm-permissions-entities-wrapper" class="ccm-permissions-entities-wrapper"><div id="ccm-permissions-entity-base" class="ccm-permissions-entity-base">' . $this->getAccessRow($tp) . '</div>';
		
		foreach($gArray as $g) { 
			$html .= $this->getAccessRow($tp, $g);
		}
		
		foreach($uArray as $ui) {
			$html .= $this->getAccessRow($tp, $ui);
		}
		
		
		$html .= '</div>';
		
		return $html;
	}
	
	public function getAccessRow($tps, $obj = false) {

		$form = Loader::helper('form');
		$html = '<div class="ccm-sitemap-permissions-entity">';

		if ($obj != false) {
			if (is_a($obj, 'Group')) {
				$identifier = 'gID_' . $obj->getGroupID();
				$name = $obj->getGroupName();
			} else if (is_a($obj, 'UserInfo')) {
				$identifier = 'uID_' . $obj->getUserID();
				$name = $obj->getUserName();
			}
		}

		$html .= $form->hidden('selectedEntity[]', $identifier);
		
		$html .= '<h2>';
		if (($identifier != 'gID_1' && $identifier != 'gID_2')) {
			$html .= '<a href="javascript:void(0)" class="ccm-permissions-remove"><img src="' . ASSETS_URL_IMAGES . '/icons/remove.png" width="16" height="16" /></a>';
		}
		$html .= '<span>' . $name . '</span></h2>';

		
		$html .= '<table border="0" cellspacing="0" cellpadding="0" id="ccm-sitemap-permissions-grid">';
		$tasks = $tps->getTaskPermissions();
		foreach($tasks as $tp) {
			$tpID = $tp->getTaskPermissionID();
			if (is_object($obj)) {
				$canRead = $tp->can($obj);
			}
			
			if ($identifier != '') {
				$id = $identifier . '_';
			}
			
			$html .= '<tr class="ccm-permissions-access">
				<th>' . $tp->getTaskPermissionName() . '</th>
				<td>' . $form->radio($id . $tpID, '1', $canRead) . ' ' . t('Yes') . '</td>
				<td>' . $form->radio($id . $tpID, 0, $canRead) . ' ' . t('No') . '</td>
			</tr>';
		}
		
		$html .= '</table></div><br/>';
		return $html;
	}
}