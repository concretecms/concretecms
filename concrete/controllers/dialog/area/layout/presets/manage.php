<?php
namespace Concrete\Controller\Dialog\Area\Layout\Presets;

use Concrete\Controller\Dialog\Area\Layout\Presets;
use Concrete\Core\Area\Layout\Layout;
use Concrete\Core\Area\Layout\Preset\UserPreset;
use Concrete\Core\Page\EditResponse;
use Exception;

class Manage extends Presets
{
    protected $viewPath = '/dialogs/area/layout/presets/manage';

    public function viewPresets()
    {
        $presets = UserPreset::getList();
        $this->set('presets', $presets);
    }

    public function delete()
    {
        if ($this->validateAction()) {
            $preset = UserPreset::getByID($this->request->request('arLayoutPresetID'));
            if (!is_object($preset)) {
                throw new Exception(t('Invalid layout preset object.'));
            }
            $preset->delete();
            $pr = new EditResponse();
            $pr->setAdditionalDataAttribute('arLayoutPresetID', $preset->getAreaLayoutPresetID());
            $pr->outputJSON();
        }
    }
}
