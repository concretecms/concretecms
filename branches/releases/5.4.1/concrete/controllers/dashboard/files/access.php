<?php 
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardFilesAccessController extends Controller {

	var $helpers = array('form','concrete/interface','validation/token', 'concrete/file');
	
	public function view($updated=false) {
		$helper_file = Loader::helper('concrete/file');
		
		$file_access_file_types = UPLOAD_FILE_EXTENSIONS_ALLOWED;
		
		//is nothing's been defined, display the constant value
		if (!$file_access_file_types) {
			$file_access_file_types = $helper_file->unserializeUploadFileExtensions(UPLOAD_FILE_EXTENSIONS_ALLOWED);
		}
		else {
			$file_access_file_types = $helper_file->unserializeUploadFileExtensions($file_access_file_types);		
		}
		$file_access_file_types = join(', ',$file_access_file_types);		
		$this->set('file_access_file_types', $file_access_file_types);		
		
		Loader::model('file_storage_location');
		$fsl = FileStorageLocation::getByID(FileStorageLocation::ALTERNATE_ID);
		if (is_object($fsl)) {
			$this->set('fslName', $fsl->getName());
			$this->set('fslDirectory', $fsl->getDirectory());
		}
		
		switch ($updated) {
			case 'extensions-saved':
				$this->set('message',t('Changes Saved'));
				break;
		}
	}
	
	public function on_start() {
		$html = Loader::helper('html');
		$this->addHeaderItem($html->css('ccm.filemanager.css'));
		$this->addHeaderItem($html->javascript('ccm.filemanager.js'));
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
		$html .= '<h2>';
		if (($identifier != 'gID_1' && $identifier != 'gID_2')) {
			$html .= '<a href="javascript:void(0)" class="ccm-file-permissions-remove"><img src="' . ASSETS_URL_IMAGES . '/icons/remove.png" width="16" height="16" /></a>';
		}
		$html .= '<span>' . $name . '</span></h2>';

		$viewExtended = (FilePermissions::PTYPE_NONE == $canSearch) ? 'style="display: none"' : '';
		
		$html .= '<table border="0" cellspacing="0" cellpadding="0" id="ccm-file-permissions-grid">
		<tr class="ccm-file-access-view">
			<th>' . t('View Site Files') . '</th>
			<td>' . $form->radio('canRead' . $id, FilePermissions::PTYPE_ALL, $canRead) . ' ' . t('Yes') . '</td>
			<td>' . $form->radio('canRead' . $id, FilePermissions::PTYPE_NONE, $canRead) . ' ' . t('No') . '</td>
		</tr>';		
			$html .= '<tr class="ccm-file-access-file-manager">';
			if ($type == 'GLOBAL') {
				$html .= '<th>' . t('Search Files') . '</th>';
			} else {
				$html .= '<th>' . t('Search Files in Set') . '</th>';
			}
			$html .= '<td>' . $form->radio('canSearch' . $id, FilePermissions::PTYPE_ALL, $canSearch) . ' ' . t('All') . '</td>
				<td>' . $form->radio('canSearch' . $id, FilePermissions::PTYPE_MINE, $canSearch) . ' ' . t('Mine') . '</td>
				<td>' . $form->radio('canSearch' . $id, FilePermissions::PTYPE_NONE, $canSearch) . ' ' . t('No') . '</td>
			</tr>';
		$html .='
		<tr class="ccm-file-access-edit" ' . $viewExtended . '>
			<th>' . t('Edit Files') . '</th>
			<td>' . $form->radio('canWrite' . $id, FilePermissions::PTYPE_ALL, $canWrite) . ' ' . t('All') . '</td>
			<td>' . $form->radio('canWrite' . $id, FilePermissions::PTYPE_MINE, $canWrite) . ' ' . t('Mine') . '</td>
			<td>' . $form->radio('canWrite' . $id, FilePermissions::PTYPE_NONE, $canWrite) . ' ' . t('None') . '</td>
		</tr>
		<tr class="ccm-file-access-admin" ' . $viewExtended . '>
			<th>' . t('Admin Files') . '</th>
			<td>' . $form->radio('canAdmin' . $id, FilePermissions::PTYPE_ALL, $canAdmin) . ' ' . t('All') . '</td>
			<td>' . $form->radio('canAdmin' . $id, FilePermissions::PTYPE_MINE, $canAdmin) . ' ' . t('Mine') . '</td>
			<td>' . $form->radio('canAdmin' . $id, FilePermissions::PTYPE_NONE, $canAdmin) . ' ' . t('None') . '</td>
		</tr>
		<tr class="ccm-file-access-add" ' . $viewExtended . '>
			<th>' . t('Add Files') . '</th>
			<td>' . $form->radio('canAdd' . $id, FilePermissions::PTYPE_ALL, $canAdd) . ' ' . t('All') . '</td>
			<td>' . $form->radio('canAdd' . $id, FilePermissions::PTYPE_CUSTOM, $canAdd) . ' ' . t('Custom') . '</td>
			<td>' . $form->radio('canAdd' . $id, FilePermissions::PTYPE_NONE, $canAdd) . ' ' . t('None') . '</td>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<td colspan="3">';
			
			$disp = ($canAdd == FilePermissions::PTYPE_CUSTOM && $canSearch != FilePermissions::PTYPE_NONE) ? 'block' : 'none';
			
			$html .= '<div class="ccm-file-access-add-extensions" style="display: ' . $disp . '; padding-top: 8px">
			
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
			$html .= '</div></div>		
			</td>
		</tr>
		</table></div>';
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
		$this->redirect('/dashboard/files/access', 'global_permissions_saved');
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

	public function storage_saved() {
		$this->set('message', t('File storage locations saved.'));
		$this->view();
	}

	public function file_storage(){
		$helper_file = Loader::helper('concrete/file');
		$validation_token = Loader::helper('validation/token');
		
		if (!$validation_token->validate("file_storage")) {
			$this->set('error', array($validation_token->getErrorMessage()));
			return;
		}
		
		Config::save('DIR_FILES_UPLOADED', $this->post('DIR_FILES_UPLOADED'));

		if ($this->post('fslName') != '' && $this->post('fslDirectory') != '') {
			Loader::model('file_storage_location');
			$fsl = FileStorageLocation::getByID(FileStorageLocation::ALTERNATE_ID);
			if (!is_object($fsl)) {
				FileStorageLocation::add($this->post('fslName'), $this->post('fslDirectory'), FileStorageLocation::ALTERNATE_ID);
			} else {
				$fsl->update($this->post('fslName'), $this->post('fslDirectory'));
			}			
		}

		$this->redirect('/dashboard/files/access','storage_saved');
	}
	
	
	public function file_access_extensions(){
		$helper_file = Loader::helper('concrete/file');
		$validation_token = Loader::helper('validation/token');
		
		if (!$validation_token->validate("file_access_extensions")) {
			$this->set('error', array($validation_token->getErrorMessage()));
			return;
		}
		
		$types = preg_split('{,}',$this->post('file-access-file-types'),null,PREG_SPLIT_NO_EMPTY);
		$types = $helper_file->serializeUploadFileExtensions($types);
		Config::save('UPLOAD_FILE_EXTENSIONS_ALLOWED',$types);
		$this->redirect('/dashboard/files/access','extensions-saved');
	}
}

?>