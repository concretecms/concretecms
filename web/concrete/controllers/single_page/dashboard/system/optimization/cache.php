<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Optimization;

use Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Core;
use User;

class Cache extends DashboardPageController
{
    public $helpers = array('form');

    public function view()
    {
    }

    public function update_cache()
    {
        if ($this->token->validate('update_cache')) {
            if ($this->isPost()) {
                $u = new User();
                $eca = $this->post('ENABLE_BLOCK_CACHE') == 1 ? 1 : 0;
                $eoc = $this->post('ENABLE_OVERRIDE_CACHE') == 1 ? 1 : 0;
                $eac = $this->post('ENABLE_ASSET_CACHE') == 1 ? 1 : 0;
                $tcc = $this->post('ENABLE_THEME_CSS_CACHE') == 1 ? 1 : 0;
                $ctc = $this->post('COMPRESS_THEME_PREPROCESSOR_OUTPUT') == 1 ? 1 : 0;

                $cms = Core::make('app');
                $cms->clearCaches();

                Config::save('concrete.cache.blocks', !!$eca);
                Config::save('concrete.cache.assets', !!$eac);
                Config::save('concrete.cache.theme_css', !!$tcc);
                Config::save('concrete.theme.compress_preprocessor_output', !!$ctc);
                Config::save('concrete.theme.generate_less_sourcemap', !!$this->post('GENERATE_LESS_SOURCEMAP'));
                Config::save('concrete.cache.overrides', !!$eoc);
                Config::save('concrete.cache.pages', $this->post('FULL_PAGE_CACHE_GLOBAL'));
                Config::save('concrete.cache.full_page_lifetime', $this->post('FULL_PAGE_CACHE_LIFETIME'));
                Config::save('concrete.cache.full_page_lifetime_value', $this->post('FULL_PAGE_CACHE_LIFETIME_CUSTOM'));
                $this->redirect('/dashboard/system/optimization/cache', 'cache_updated');
            }
        } else {
            $this->set('error', array($this->token->getErrorMessage()));
        }
    }

    public function cache_updated()
    {
        $this->set('message', t('Cache settings saved.'));
        $this->view();
    }
}
