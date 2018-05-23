<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;

use Concrete\Core\Page\Controller\DashboardSitePageController;
use Config;
use Core;
use View;

class Icons extends DashboardSitePageController
{
    public $helpers = ['form', 'concrete/asset_library', 'json'];

    public function on_start()
    {
        parent::on_start();
        $this->requireAsset()
        $this->set('config', $this->getSite()->getConfigRepository());
    }

    public function icons_saved()
    {
        $this->set('message', t('Icons updated successfully.'));
    }

    public function update_icons()
    {
        $config = $this->getSite()->getConfigRepository();
        if ($this->token->validate('update_icons')) {
            $s = Core::make('helper/security');
            $faviconFID = $s->sanitizeInt($this->post('faviconFID'));
            $iosHomeFID = $s->sanitizeInt($this->post('iosHomeFID'));
            $modernThumbFID = $s->sanitizeInt($this->post('modernThumbFID'));
            $modernThumbBG = $s->sanitizeString($this->post('modernThumbBG'));

            $config->save('misc.favicon_fid', (int) $faviconFID);
            $config->save('misc.iphone_home_screen_thumbnail_fid', (int) $iosHomeFID);
            $config->save('misc.modern_tile_thumbnail_fid', (int) $modernThumbFID);
            $config->save('misc.modern_tile_thumbnail_bgcolor', $modernThumbBG);

            $this->redirect('/dashboard/system/basics/icons/', 'icons_saved');
        } else {
            $this->set('error', [$this->token->getErrorMessage()]);
        }
    }
}
