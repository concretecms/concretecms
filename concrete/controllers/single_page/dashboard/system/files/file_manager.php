<?php

namespace Concrete\Controller\SinglePage\Dashboard\System\Files;

use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Controller\DashboardPageController;
use Symfony\Component\HttpFoundation\Response;

class FileManager extends DashboardPageController
{
    public function view(): void
    {
        $this->set('keepFoldersOnTop', $this->app['config']->get('concrete.file_manager.keep_folders_on_top'));
    }

    public function submit(): ?Response
    {
        if (!$this->token->validate('save_file_manager_settings')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if (!$this->error->has()) {
            $this->app['config']->save('concrete.file_manager.keep_folders_on_top', (bool) $this->post('keepFoldersOnTop'));

            $this->flash('success', t('Settings saved successfully.'));

            return $this->app->make(ResponseFactoryInterface::class)->redirect($this->action(''), 302);
        }

        return null;
    }
}
