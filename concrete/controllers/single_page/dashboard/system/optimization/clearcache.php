<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Optimization;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Controller\DashboardPageController;
use Core;
use Symfony\Component\HttpFoundation\Request;

class Clearcache extends DashboardPageController
{
    public $helpers = array('form');

    public function view()
    {
    }

    public function do_clear()
    {
        if ($this->token->validate("clear_cache")) {
            if ($this->isPost()) {
                $thumbnails = $this->request('thumbnails') === '1';
                $config = $this->app->make(Repository::class);
                $config->set('concrete.cache.clear.thumbnails', $thumbnails);
                $config->save('concrete.cache.clear.thumbnails', $thumbnails);
                $this->app->clearCaches();
                $this->redirect('/dashboard/system/optimization/clearcache', 'cache_cleared');
            }
        } else {
            $this->set('error', array($this->token->getErrorMessage()));
        }
    }

    public function cache_cleared()
    {
        $this->set('message', t('Cached files removed.'));
        $this->view();
    }
}
