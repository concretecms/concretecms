<?
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dialogs_Area_Layout_Presets extends BackendInterfaceController {

	protected $viewPath = '/system/dialogs/area/layout/presets';

	protected function canAccess() {
		$pk = PermissionKey::getByHandle('manage_layout_presets');
		return $pk->validate();
	}

	public function view($arLayoutID, $token) {
		$arLayout = AreaLayout::getByID($arLayoutID);
		if (!is_object($arLayout)) {
			throw new Exception(t('Invalid layout object.'));
		}

		$presetlist = AreaLayoutPreset::getList();
		$presets = array();
		$presets['-1'] = t('** New');
		foreach($presetlist as $preset) {
			$presets[$preset->getAreaLayoutPresetID()] = $preset->getAreaLayoutPresetName();
		}

		$this->set('arLayout', $arLayout);
		$this->set('presets', $presets);
	}


	public function submit($arLayoutID) {
		if ($this->validateAction()) {
			$arLayout = AreaLayout::getByID($arLayoutID);
			if (!is_object($arLayout)) {
				throw new Exception(t('Invalid layout object.'));
			}
			if ($_POST['arLayoutPresetID'] == '-1') {
				$preset = AreaLayoutPreset::add($arLayout, $_POST['arLayoutPresetName']);
			} else {
				$existingPreset = AreaLayoutPreset::getByID($_POST['arLayoutPresetID']);
				if (is_object($existingPreset)) {
					$existingPreset->updateAreaLayoutObject($arLayout);
				}
			}

			$pr = new PageEditResponse();
			if ($existingPreset) {
				$pr->setMessage(t('Area layout preset updated successfully.'));
			} else {
				$pr->setMessage(t('Area layout preset saved successfully.'));
			}
			Loader::helper('ajax')->sendResult($pr);
			
		}		
	}
}

