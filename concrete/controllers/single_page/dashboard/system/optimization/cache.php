<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Optimization;

use Concrete\Core\Page\Controller\DashboardPageController;

class Cache extends DashboardPageController
{
    public function view()
    {
        $this->set('dateService', $this->app->make('date'));
        $config = $this->app->make('config');
        $this->set('enableBlockCache', (bool) $config->get('concrete.cache.blocks'));
        $this->set('enableAssetCache', (bool) $config->get('concrete.cache.assets'));
        $this->set('enableThemeCssCache', (bool) $config->get('concrete.cache.theme_css'));
        $this->set('compressThemePreprocessorOutput', (bool) $config->get('concrete.theme.compress_preprocessor_output'));
        $this->set('generateLessSourcemap', (bool) $config->get('concrete.theme.generate_less_sourcemap'));
        $this->set('enableOverrideCache', (bool) $config->get('concrete.cache.overrides'));
        $this->set('fullPageCacheGlobal', (string) $config->get('concrete.cache.pages'));
        $this->set('fullPageCacheLifetime', (string) $config->get('concrete.cache.full_page_lifetime'));
        $this->set('defaultCacheLifetime', (int) $config->get('concrete.cache.lifetime'));
        $this->set('fullPageCacheCustomLifetime', (int) $config->get('concrete.cache.full_page_lifetime_value') ?: null);
    }

    public function update_cache()
    {
        $post = $this->request->request;
        if (!$this->token->validate('update_cache')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if ($post->get('FULL_PAGE_CACHE_LIFETIME') === 'custom') {
            $customFullPageLifetimeValue = (int) $post->get('FULL_PAGE_CACHE_LIFETIME_CUSTOM');
            if ($customFullPageLifetimeValue < 1) {
                $this->error->add(t('Please specify the custom value of the full page cache.'));
            }
        }
        if ($this->error->has()) {
            return $this->view();
        }
        $config = $this->app->make('config');
        $config->save('concrete.cache.blocks', (bool) $post->get('ENABLE_BLOCK_CACHE'));
        $config->save('concrete.cache.assets', (bool) $post->get('ENABLE_ASSET_CACHE'));
        $config->save('concrete.cache.theme_css', (bool) $post->get('ENABLE_THEME_CSS_CACHE'));
        $config->save('concrete.theme.compress_preprocessor_output', (bool) $post->get('COMPRESS_THEME_PREPROCESSOR_OUTPUT'));
        $config->save('concrete.theme.generate_less_sourcemap', (bool) $this->post('GENERATE_LESS_SOURCEMAP'));
        $config->save('concrete.cache.overrides', (bool) $post->get('ENABLE_OVERRIDE_CACHE'));
        $config->save('concrete.cache.pages', (string) $this->post('FULL_PAGE_CACHE_GLOBAL'));
        $config->save('concrete.cache.full_page_lifetime', (string) $post->get('FULL_PAGE_CACHE_LIFETIME'));
        if (isset($customFullPageLifetimeValue)) {
            $config->save('concrete.cache.full_page_lifetime_value', $customFullPageLifetimeValue);
        }
        $this->app->clearCaches();
        $this->flash('success', t('Cache settings saved.'));

        return $this->buildRedirect($this->action(''));
    }
}
