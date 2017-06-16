<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Page\Controller\DashboardSitePageController;
use View;
use Core;

class Icons extends DashboardSitePageController {

    public $helpers = array('form', 'concrete/asset_library', 'json');

    public function on_start() {
        parent::on_start();
        $view = View::getInstance();
        $view->requireAsset('core/colorpicker');
        $this->set('config', $this->getSite()->getConfigRepository());
    }

    public function icons_saved() {
        $this->set('message', t("Icons updated successfully."));
    }

    public function update_icons() {
        $config = $this->getSite()->getConfigRepository();
        if ($this->token->validate("update_icons")) {

            $s = Core::make('helper/security');
            $appIconFID = $s->sanitizeInt($this->post('appIconFID'));
            $modernThumbBG = $s->sanitizeString($this->post('modernThumbBG'));

            $config->save('misc.app_icon_fid', intval($appIconFID));
            $config->save('misc.modern_tile_thumbnail_bgcolor', $modernThumbBG);

            $this->redirect('/dashboard/system/basics/icons/', 'icons_saved');
        } else {
            $this->set('error', array($this->token->getErrorMessage()));
        }
    }
}
