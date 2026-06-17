<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Boards;

use Concrete\Core\Page\Controller\DashboardPageController;

class Settings extends DashboardPageController
{

    public function view()
    {
        $config = $this->app->make('config');
        $logBoardInstances = (int) $config->get('concrete.log.boards.instances');
        $this->set('logBoardInstances', $logBoardInstances);
        $automaticallyRefreshInstances = (int) $config->get('concrete.boards.automatically_refresh_instances');
        $this->set('automaticallyRefreshInstances', $automaticallyRefreshInstances);
    }

    public function save()
    {
        if (!$this->token->validate('save')) {
            $this->error->add($this->token->getErrorMessage());
        }
        if (!$this->error->has()) {
            $config = $this->app->make('config');
            $logBoardInstances = $this->request->request->getBoolean('log_board_instances');
            $config->save('concrete.log.boards.instances', $logBoardInstances);
            $automaticallyRefreshInstances = $this->request->request->getBoolean('automatically_refresh_instances');
            $config->save('concrete.boards.automatically_refresh_instances', $automaticallyRefreshInstances);
            $this->flash('success', t('Board settings saved.'));
            return $this->buildRedirect($this->action('view'));
        }
        $this->view();
    }
}
