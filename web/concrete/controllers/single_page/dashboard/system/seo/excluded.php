<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Page\Controller\DashboardPageController;
use Config;
use Loader;
use URLify;

class Excluded extends DashboardPageController
{

    public function save()
    {

        $token = $this->app['token'];
        $config = $this->app['config'];
        $security = $this->app['helper/security'];

        if ($token->validate('excluded_words_save')) {
            $words = $security->sanitizeString($this->post('SEO_EXCLUDE_WORDS'));
            $config->save('concrete.seo.exclude_words', $words);

            $this->redirect('/dashboard/system/seo/excluded', 'saved');
        } else {
            $this->error->add('Invalid token.');
            $this->view();
        }
    }

    public function reset()
    {
        $this->app['config']->save("concrete.seo.exclude_words", implode(', ', URLify::$remove_list));
        $this->redirect('/dashboard/system/seo/excluded', 'reset_complete');
    }

    public function view($message = false)
    {
        $this->set('app', $this->app);
        $config = $this->app['config'];

        if ($message) {
            switch ($message) {
                case 'reset_complete':
                    $this->set('message', t('Reserved words reset.'));
                    break;
                case 'saved':
                    $this->set('success', t('Reserved words updated.'));
                    break;
            }
        }
        $this->set('SEO_EXCLUDE_WORDS_ORIGINAL_ARRAY', Urlify::$remove_list);
        $excludeSeoWords = $config->get('concrete.seo.exclude_words');
        if (is_string($excludeSeoWords)) {
            if (strlen($excludeSeoWords)) {
                $remove_list = explode(',', $excludeSeoWords);
                $remove_list = array_map('trim', $remove_list);
                $remove_list = array_filter($remove_list, 'strlen');
            } else {
                $remove_list = array();
            }
        } else {
            $remove_list = Urlify::$remove_list;
        }
        $this->set('SEO_EXCLUDE_WORDS_ARRAY', $remove_list);
        $this->set('SEO_EXCLUDE_WORDS', Config::get('concrete.seo.exclude_words'));
    }

}
