<?php
namespace Concrete\Controller\Dialog\Area\Layout;

use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Page\EditResponse;
use PermissionKey;
use Exception;
use Loader;

use \Concrete\Core\Area\Layout\Preset as AreaLayoutPreset;
use \Concrete\Core\Area\Layout\Layout as AreaLayout;

class Presets extends BackendInterfaceController
{

    protected $viewPath = '/dialogs/area/layout/presets';

    protected function canAccess()
    {
        $pk = PermissionKey::getByHandle('manage_layout_presets');
        return $pk->validate();
    }

    public function view($arLayoutID)
    {
        $arLayout = AreaLayout::getByID($arLayoutID);
        if (!is_object($arLayout)) {
            throw new Exception(t('Invalid layout object.'));
        }

        $presetlist = AreaLayoutPreset::getList();
        $presets = array();
        $presets['-1'] = t('** New');
        foreach ($presetlist as $preset) {
            $presets[$preset->getAreaLayoutPresetID()] = $preset->getAreaLayoutPresetName();
        }

        $this->set('arLayout', $arLayout);
        $this->set('presets', $presets);
    }

    public function getPresetData($arLayoutPresetID)
    {
        $existingPreset = AreaLayoutPreset::getByID($arLayoutPresetID);
        if (is_object($existingPreset)) {
            $r = new \stdClass;
            $arLayout = $existingPreset->getAreaLayoutObject();
            $r->arLayout = $arLayout;
            $r->arLayoutColumns = $arLayout->getAreaLayoutColumns();
            \Core::make('helper/ajax')->sendResult($r);
        }
    }

    public function submit($arLayoutID)
    {
        if ($this->validateAction()) {
            $arLayout = AreaLayout::getByID($arLayoutID);
            if (!is_object($arLayout)) {
                throw new Exception(t('Invalid layout object.'));
            }
            if ($_POST['arLayoutPresetID'] == '-1') {
                AreaLayoutPreset::add($arLayout, $_POST['arLayoutPresetName']);
            } else {
                $existingPreset = AreaLayoutPreset::getByID($_POST['arLayoutPresetID']);
                if (is_object($existingPreset)) {
                    $existingPreset->updateName($_POST['arLayoutPresetName']);
                    $existingPreset->updateAreaLayoutObject($arLayout);
                }
            }

            $pr = new EditResponse();
            if ($existingPreset) {
                $pr->setMessage(t('Area layout preset updated successfully.'));
            } else {
                $pr->setMessage(t('Area layout preset saved successfully.'));
            }
            $pr->outputJSON();

        }
    }
}

