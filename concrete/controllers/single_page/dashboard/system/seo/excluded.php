<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Page\Controller\DashboardPageController;
use URLify;

class Excluded extends DashboardPageController
{
    public function view($message = false)
    {
        $config = $this->app->make('config');
        $this->set('defaultExcludedWords', $this->getDefaultExcludedWords());
        $excludedWords = $config->get('concrete.seo.exclude_words');
        if (is_string($excludedWords)) {
            $excludedWords = preg_split('/\s*,\s*/', $excludedWords, -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $excludedWords = $this->getDefaultExcludedWords();
        }
        $this->set('excludedWords', $excludedWords);
    }

    public function save()
    {
        $config = $this->app->make('config');
        $security = $this->app->make('helper/security');
        if (!$this->token->validate('excluded_words_save')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $words = $security->sanitizeString($this->request->request->get('SEO_EXCLUDE_WORDS', ''));
        $words = implode(', ', preg_split('/\s*,\s*/', $words, -1, PREG_SPLIT_NO_EMPTY));
        if ($this->error->has()) {
            return $this->view();
        }
        $config->save('concrete.seo.exclude_words', $words);
        $this->flash('success', t('Reserved words updated.'));

        return $this->buildRedirect($this->action());
    }

    public function reset()
    {
        $config = $this->app->make('config');
        $config->save('concrete.seo.exclude_words', implode(', ', $this->getDefaultExcludedWords()));

        $this->flash('success', t('Reserved words reset.'));

        return $this->buildRedirect($this->action());
    }

    /**
     * @return string[]
     */
    protected function getDefaultExcludedWords(): array
    {
        return Urlify::$remove_list['en'];
    }
}
