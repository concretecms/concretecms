<?php

namespace Concrete\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;
use Concrete\Core\View\DialogView;

class Welcome extends DashboardPageController
{
    /**
     * @var bool
     */
    protected $isExternalDialog;

    public function __construct(Page $c)
    {
        parent::__construct($c);
        $this->isExternalDialog = $this->request->query->get('_ccm_dashboard_external') === '1';
        if ($this->isExternalDialog) {
            $this->view = new DialogView('dashboard_full');
            $this->view->setViewRootDirectoryName('themes/dashboard');
            $this->setTheme('dashboard');
            $this->set('c', $c);
        }
    }

    public function view()
    {
        if ($this->isExternalDialog) {
            $this->setThemeViewTemplate('dialog.php');
        } else {
            $this->setThemeViewTemplate('desktop.php');
        }
    }
}
