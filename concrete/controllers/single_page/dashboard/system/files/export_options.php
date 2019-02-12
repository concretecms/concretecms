<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;

class ExportOptions extends DashboardPageController
{
    public function view()
    {
        $config = $this->app->make('config');
        $this->set('csvAddBom', (bool) $config->get('concrete.export.csv.include_bom'));
    }

    public function submit()
    {
        $post = $this->request->request;
        $config = $this->app->make('config');
        if (!$this->token->validate('ccm-export-options')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if ($this->error->has()) {
            return $this->view();
        }
        $config->save('concrete.export.csv.include_bom', (bool) $post->get('csvAddBom'));
        $this->flash('success', t('Options saved successfully.'));

        return $this->app->make(ResponseFactoryInterface::class)->redirect($this->action(''), 302);
    }
}
