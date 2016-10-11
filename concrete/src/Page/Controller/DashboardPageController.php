<?php
namespace Concrete\Core\Page\Controller;

use Doctrine\ORM\EntityManager;
use Concrete\Core\Cookie\CookieJar;
use Concrete\Core\Application\Service\DashboardMenu;

class DashboardPageController extends PageController
{
    /**
     * @var \Concrete\Core\Error\ErrorList\ErrorList
     */
    protected $error;

    /**
     * @var \Concrete\Core\Validation\CSRF\Token
     */
    public $token;

    /**
     * @var string[]
     */
    protected $helpers = ['form'];

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function enableNativeMobile()
    {
        $md = new \Mobile_Detect();
        if ($md->isMobile()) {
            $this->addHeaderItem('<meta name="viewport" content="width=device-width,initial-scale=1"/>');
        }
    }

    public function on_start()
    {
        $this->token = $this->app->make('helper/validation/token');
        $this->error = $this->app->make('helper/validation/error');
        $this->set('interface', $this->app->make('helper/concrete/ui'));
        $this->set('dashboard', $this->app->make('helper/concrete/dashboard'));

        $this->entityManager = $this->app->make(EntityManager::class);

        $hideDashboardPanel = false;
        $cookie = $this->app->make(CookieJar::class);
        if ($cookie->has('dashboardPanelStatus') && $cookie->get('dashboardPanelStatus') === 'closed') {
            $hideDashboardPanel = true;
        }
        $this->set('hideDashboardPanel', $hideDashboardPanel);
        $dh = DashboardMenu::getMine();
        if ($dh->contains($this->getPageObject())) {
            $this->set("_bookmarked", true);
        } else {
            $this->set('_bookmarked', false);
        }
    }

    public function on_before_render()
    {
        $pageTitle = $this->get('pageTitle');
        if ($pageTitle === null || $pageTitle === '' || $pageTitle === false) {
            $this->set('pageTitle', $this->c->getCollectionName());
        }
        $this->set('token', $this->token);
        $this->set('error', $this->error);
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }
}
