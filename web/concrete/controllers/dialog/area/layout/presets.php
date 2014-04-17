<?
namespace Concrete\Controller\Dialog\Area\Layout;
use \Concrete\Controller\Backend\UI as BackendInterfaceController;
use PermissionKey;
use Exception;
use Loader;

use \Concrete\Core\Area\Layout\Preset as AreaLayoutPreset;
use \Concrete\Core\Area\Layout\Layout as AreaLayout;
use \Concrete\Core\Page\EditReponse as PageEditResponse;

class Presets extends BackendInterfaceController {

	protected $viewPath = '/dialogs/area/layout/presets';

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

