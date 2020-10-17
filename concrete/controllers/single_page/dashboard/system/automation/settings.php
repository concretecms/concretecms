<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Automation;

use Concrete\Core\Page\Controller\DashboardPageController;

class Settings extends DashboardPageController
{

    public function view()
    {
        $this->set('listening', (string) $this->app->make('config')->get('concrete.messenger.consume.method'));
    }

    public function submit()
    {
        if (!$this->token->validate('submit')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $config = $this->app->make('config');
            $listening = $this->request->request->get('listening') === 'worker' ? 'worker' : 'app';
            $config->save('concrete.messenger.consume.method', $listening);
            $this->flash('success', t('Automation settings saved.'));
            return $this->buildRedirect([$this->getPageObject(), 'view']);
        }
    }
}
