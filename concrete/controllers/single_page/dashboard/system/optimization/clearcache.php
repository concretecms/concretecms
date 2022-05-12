<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Optimization;

use Concrete\Core\Page\Controller\DashboardPageController;

class Clearcache extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('clearThumbnails', (bool) $config->get('concrete.cache.clear.thumbnails'));
    }

    public function do_clear()
    {
        $post = $this->request->request;
        if (!$this->token->validate('clear_cache')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if ($this->error->has()) {
            return $this->view();
        }
        $clearThumbnails = (bool) $post->get('thumbnails');
        $config = $this->app->make('config');
        $config->set('concrete.cache.clear.thumbnails', $clearThumbnails);
        $config->save('concrete.cache.clear.thumbnails', $clearThumbnails);
        $this->app->clearCaches();
        $timestamp = time();
        $config->set('concrete.cache.last_cleared', $timestamp);
        $config->save('concrete.cache.last_cleared', $timestamp);
        $this->flash('success', t('Cached files removed.'));

        return $this->buildRedirect($this->action(''));
    }
}
