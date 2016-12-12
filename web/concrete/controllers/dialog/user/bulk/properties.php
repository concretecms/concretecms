<?php
namespace Concrete\Controller\Dialog\User\Bulk;

use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use PermissionKey;
use \Concrete\Core\Http\ResponseAssetGroup;
use Permissions;
use \Concrete\Core\User\EditResponse as UserEditResponse;
use UserAttributeKey;
use Loader;
use UserInfo;
use Exception;

class Properties extends BackendInterfaceController {

	protected $viewPath = '/dialogs/user/bulk/properties';
	protected $users = array();
	protected $canEdit = false;

	protected function canAccess() {
		$tp = Loader::helper('concrete/user');
		if ($tp->canAccessUserSearchInterface()) {
			$this->populateUsers();
		}
		return $this->canEdit;
	}

	protected function populateUsers() {
		if (is_array($_REQUEST['item'])) {
			foreach($_REQUEST['item'] as $uID) {
				$ui = UserInfo::getByID($uID);
				if (is_object($ui) && !$ui->isError()) {
					$this->users[] = $ui;
				}
			}
		}

		$allowedEditAttributes = array();
		$pk = PermissionKey::getByHandle('edit_user_properties');
		$assignment = $pk->getMyAssignment();
		if (is_object($assignment)) {
			$this->allowedEditAttributes = $assignment->getAttributesAllowedArray();
			$this->set('allowedEditAttributes', $this->allowedEditAttributes);
		}
		if (count($this->users) > 0) {
			$this->canEdit = true;
			foreach($this->users as $ui) {
				$up = new Permissions($ui);
				if (!$up->canEditUser()) {
					$this->canEdit = false;
				}
			}
		} else {
			$this->canEdit = false;
		}
		return $this->canEdit;
	}

	public function view() {
		$r = ResponseAssetGroup::get();
		$r->requireAsset('core/app/editable-fields');
		$this->populateUsers();
		$form = Loader::helper('form');
		$attribs = UserAttributeKey::getList();
		$this->set('users', $this->users);
		$this->set('attributes', $attribs);
	}

	public function updateAttribute() {
		$ur = new UserEditResponse();
		$ak = UserAttributeKey::getByID($_REQUEST['name']);
		if ($this->validateAction()) {
			$this->populateUsers();
			if ($this->canEdit && in_array($ak->getAttributeKeyID(), $this->allowedEditAttributes)) {
				foreach($this->users as $ui) {
                    $ui->saveUserAttributesForm(array($ak));
					$ui->reindex();
				}
				$ur->setUsers($this->users);
				$val = $ui->getAttributeValueObject($ak);
				$ur->setAdditionalDataAttribute('value',  $val->getValue('displaySanitized','display'));
				$ur->setMessage(t('Users updated successfully.'));
			} else {
				throw new Exception(t("You don't have access to update this attribute."));
			}
		}
		$ur->outputJSON();
	}

	public function clearAttribute() {
		$ur = new UserEditResponse();
		$ak = UserAttributeKey::getByID($_REQUEST['akID']);
		if ($this->validateAction()) {
			$this->populateUsers();
			if ($this->canEdit && in_array($ak->getAttributeKeyID(), $this->allowedEditAttributes)) {
				foreach($this->users as $ui) {
					$ui->clearAttribute($ak);			
					$ui->reindex();
				}
				$ur->setUsers($this->users);
				$ur->setAdditionalDataAttribute('value',  false);
				$ur->setMessage(t('Attributes cleared successfully.'));
			} else {
				throw new Exception(t("You don't have access to update this attribute."));
			}
		}
		$ur->outputJSON();

	}



}

