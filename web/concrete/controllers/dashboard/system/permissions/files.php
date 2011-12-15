<?
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardSystemPermissionsFilesController extends DashboardBaseController {

	var $helpers = array('form','concrete/interface','validation/token', 'concrete/file');
	
	public function view() {
		$helper_file = Loader::helper('concrete/file');
	}
	
	public function getFileAccessRow($type, $identifier = '', $name = '', $canSearch = true, $canRead = FilePermissions::PTYPE_ALL, $canWrite = FilePermissions::PTYPE_ALL, $canAdmin = FilePermissions::PTYPE_ALL, $canAdd = FilePermissions::PTYPE_ALL, $allowedExtensions = array()) {

		$concrete_file = Loader::helper("concrete/file");
		$form = Loader::helper('form');
		
		$html = '<div class="ccm-file-permissions-entity">';
		
		$html .= $form->hidden('selectedEntity[]', $identifier);
		
		$ida = '';
		$id = '';
		
		if ($identifier != '') {
			$id = '_'. $identifier;
			$ida = '_' . $identifier . '[]';
		}
		$html .= '<h3>';
		if (($identifier != 'gID_1' && $identifier != 'gID_2')) {
			$html .= '<a href="javascript:void(0)" class="ccm-file-permissions-remove"><img src="' . ASSETS_URL_IMAGES . '/icons/remove.png" width="16" height="16" /></a>';
		}
		$html .= '<span>' . t($name) . '</span></h3>';

		$viewExtended = (FilePermissions::PTYPE_NONE == $canSearch) ? 'style="display: none"' : '';
		
		$html .= '
		<div class="clearfix ccm-file-access-view">
		<label>' . t('View Site Files') . '</label>
		<div class="input">' . $form->select('canRead' . $id, array(FilePermissions::PTYPE_ALL => t('Yes'), FilePermissions::PTYPE_NONE => t('No')), $canRead) . '</div>
		</div>';		
			$html .= '<div class="clearfix ccm-file-access-file-manager">';
			if ($type == 'GLOBAL') {
				$html .= '<label>' . t('Search Files') . '</label>';
			} else {
				$html .= '<label>' . t('Search Files in Set') . '</label>';
			}
			$html .= '
			<div class="input">' . $form->select('canSearch' . $id, array(FilePermissions::PTYPE_ALL => t('Yes'), FilePermissions::PTYPE_MINE => t('Mine'), FilePermissions::PTYPE_NONE => t('No')), $canSearch) . '</div>
		</div>
		<div class="clearfix ccm-file-access-edit" ' . $viewExtended . '>
			<label>' . t('Edit Files') . '</label>
			<div class="input">' . $form->select('canWrite' . $id, array(FilePermissions::PTYPE_ALL => t('Yes'), FilePermissions::PTYPE_MINE => t('Mine'), FilePermissions::PTYPE_NONE => t('No')), $canWrite) . '</div>
		</div>
		<div class="clearfix ccm-file-access-admin" ' . $viewExtended . '>
			<label>' . t('Admin Files') . '</label>
			<div class="input">' . $form->select('canAdmin' . $id, array(FilePermissions::PTYPE_ALL => t('Yes'), FilePermissions::PTYPE_MINE => t('Mine'), FilePermissions::PTYPE_NONE => t('No')), $canAdmin) . '</div>
		</div>
		<div class="clearfix ccm-file-access-add" ' . $viewExtended . '>
			<label>' . t('Add Files') . '</label>
			<div class="input">' . $form->select('canAdd' . $id, array(FilePermissions::PTYPE_ALL => t('Yes'), FilePermissions::PTYPE_CUSTOM => t('Custom'), FilePermissions::PTYPE_NONE => t('No')), $canAdd) . '</div>
		</div>
		';
			
			$disp = ($canAdd == FilePermissions::PTYPE_CUSTOM && $canSearch != FilePermissions::PTYPE_NONE) ? 'block' : 'none';
			
			$html .= '<div class="ccm-file-access-add-extensions" style="display: ' . $disp . ';"><div class="clearfix"><label></label><div class="input">
			
			<div class="ccm-file-access-add-extensions-header">' . $form->checkbox('toggleCanAddExtension', 1, false) . '
			<strong>' . t('Allowed File Types') . '</strong></div>
			
			<div class="ccm-file-access-extensions">';
			$extensions = $concrete_file->getAllowedFileExtensions();
			foreach($extensions as $ext) {
				$checked = false;
				if ((FilePermissions::PTYPE_CUSTOM == $canAdd && in_array($ext, $allowedExtensions)) || FilePermissions::PTYPE_ALL == $canAdd) {
					$checked = true;
				}
				$html .= '<div>' . $form->checkbox('canAddExtension' . $ida, $ext, $checked) . ' ' . $ext . '</div>';
			}
			$html .= '</div></div></div></div>
		</div>';
		return $html;
	}

	public function save_global_permissions() {
		$vt = Loader::helper('validation/token');
		
		if (!$vt->validate("file_permissions")) {
			$this->set('error', array($vt->getErrorMessage()));
			return;
		}	
		
		$p = $this->post();
		
		Loader::model('file_set');
		
		$fs = FileSet::getGlobal();
		$this->setFileSetPermissions($fs, $p);
		$this->redirect('/dashboard/system/permissions/files', 'global_permissions_saved');
	}
	
	public function setFileSetPermissions($fs, $post) {
		$fs->resetPermissions();		
		foreach($post['selectedEntity'] as $e) {
			if ($e != '') {
				$id = substr($e, 4);
				if (strpos($e, 'uID') === 0) {
					$obj = UserInfo::getByID($id);
				} else {
					$obj = Group::getByID($id);					
				}
			
				$canSearch = $post['canSearch_' . $e];
				$canRead = $post['canRead_' . $e];
				$canWrite = $post['canWrite_' . $e];
				$canAdmin = $post['canAdmin_' . $e];
				$canAdd = $post['canAdd_' . $e];
				$extensions = $post['canAddExtension_' . $e];
				
				$fs->setPermissions($obj, $canSearch, $canRead, $canWrite, $canAdmin, $canAdd, $extensions);
			}
		}	
	}
	
	public function global_permissions_saved() {
		$this->set('message', t('Global Permissions saved.'));
		$this->view();
	}


}