<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Seo;

use Concrete\Core\Page\Controller\DashboardPageController;

class UrlSlug extends DashboardPageController
{
    public function view($message = false)
    {
        $config = $this->app->make('config');
        $this->set('segmentMaxLength', (int) $config->get('concrete.seo.segment_max_length'));
        $this->set('enableSlugAsciify', (bool) $config->get('concrete.seo.enable_slug_asciify'));
    }

    public function save()
    {
        if (!$this->token->validate('url_slug_save')) {
            $this->error->add($this->token->getErrorMessage());
        }
        $segmentMaxLength = (int) $this->post('segment_max_length');
        if (!$segmentMaxLength) {
            $this->error->add(t('Please input segment length.'));
        }
        $enableSlugAsciify = (bool) $this->post('enable_slug_asciify');

        if (!$this->error->has()) {
            $config = $this->app->make('config');
            $config->save('concrete.seo.exclude_words', $segmentMaxLength);
            $config->save('concrete.seo.enable_slug_asciify', $enableSlugAsciify);
            $this->flash('success', t('URL Slug settings have been updated.'));
            return $this->buildRedirect($this->action());
        }

        $this->view();
    }
}
