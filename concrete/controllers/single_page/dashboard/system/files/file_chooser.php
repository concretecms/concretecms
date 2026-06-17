<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Symfony\Component\HttpFoundation\Response;

class FileChooser extends DashboardPageController
{
    public function view(): void
    {
        $fileChooserDefaultTab = $this->app['config']->get('concrete.file_chooser.default_tab');

        if (!$fileChooserDefaultTab) {
            $fileChooserDefaultTab = 'file_manager';
        }

        $this->set('fileChooserDefaultTab', $fileChooserDefaultTab);
    }

    public function submit(): ?Response
    {
        if (!$this->token->validate('save_file_chooser_settings')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $this->app['config']->save('concrete.file_chooser.default_tab',  $this->post('fileChooserDefaultTab'));

            $this->flash('success', t('Settings saved successfully.'));

            return $this->app->make(ResponseFactoryInterface::class)->redirect($this->action(''), 302);
        }

        return null;
    }
}
