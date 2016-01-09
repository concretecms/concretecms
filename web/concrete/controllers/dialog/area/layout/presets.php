<?php
namespace Concrete\Controller\Dialog\Area\Layout;

use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Area\Layout\Preset\Column;
use Concrete\Core\Page\EditResponse;
use HtmlObject\Element;
use PermissionKey;
use Exception;
use Loader;
use Request;
use \Concrete\Core\Area\Layout\Preset\Preset;
use \Concrete\Core\Area\Layout\Preset\UserPreset;
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

        $presetlist = UserPreset::getList();
        $presets = array();
        $presets['-1'] = t('** New');
        foreach ($presetlist as $preset) {
            $presets[$preset->getAreaLayoutPresetID()] = $preset->getAreaLayoutPresetName();
        }

        $this->set('arLayout', $arLayout);
        $this->set('presets', $presets);
    }

    public function getPresetData($cID, $arLayoutPresetID)
    {
        $c = \Page::getByID($cID, 'ACTIVE');
        $r = Request::getInstance();
        $r->setCurrentPage($c);

        $existingPreset = Preset::getByID($arLayoutPresetID);
        if (is_object($existingPreset)) {
            $r = new \stdClass;
            $formatter = $existingPreset->getFormatter();
            $container = $formatter->getPresetContainerHtmlObject();
            foreach($existingPreset->getColumns() as $column) {
                $html = $column->getColumnHtmlObjectEditMode();
                $container->appendChild($html);
            }

            $r->id = $arLayoutPresetID;
            $r->html = (string) $container;
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
                UserPreset::add($arLayout, $_POST['arLayoutPresetName']);
            } else {
                $existingPreset = UserPreset::getByID($_POST['arLayoutPresetID']);
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

