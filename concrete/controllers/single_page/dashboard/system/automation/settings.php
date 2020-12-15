<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Automation;

use Concrete\Core\Page\Controller\DashboardPageController;
use Illuminate\Filesystem\Filesystem;

class Settings extends DashboardPageController
{

    public function view()
    {
        $this->set('listening', (string) $this->app->make('config')->get('concrete.messenger.consume.method'));
        $this->set('scheduling', $this->app->make('config')->get('concrete.processes.scheduler.enable') ? 1 : 0);
        $this->set('loggingMethod', (string) $this->app->make('config')->get('concrete.processes.logging.method'));
        $this->set('logDirectory', (string) $this->app->make('config')->get('concrete.processes.logging.file.directory'));
    }

    public function submit()
    {
        $loggingMethod = 'none';
        $logDirectory = '';
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }

        if ($this->request->request->get('loggingMethod') == 'file') {
            $loggingMethod = 'file';
            $logDirectory = $this->request->request->get('logDirectory');

            $filesystem = new Filesystem();
            if (!$filesystem->exists($logDirectory)) {
                $this->error->add(t('The logging directory must exist on the web server.'));
            } else {
                if (!$filesystem->isWritable($logDirectory)) {
                    $this->error->add(t('The logging directory must be writable by the web server.'));
                }
            }

            if (substr($logDirectory, -1) != '/') {
                $this->error->add(t('The logging directory path must end with a trailing slash.'));
            }
        }

        if (!$this->error->has()) {
            $config = $this->app->make('config');
            $scheduling = $this->request->request->get('scheduling') === '1' ? true : false;
            $listening = $this->request->request->get('listening') === 'worker' ? 'worker' : 'app';
            $config->save('concrete.messenger.consume.method', $listening);
            $config->save('concrete.processes.scheduler.enable', $scheduling);
            $config->save('concrete.processes.logging.method', $loggingMethod);
            $config->save('concrete.processes.logging.file.directory', $logDirectory);
            $this->flash('success', t('Automation settings saved.'));
            return $this->buildRedirect([$this->getPageObject(), 'view']);
        }

        $this->view();
    }
}
