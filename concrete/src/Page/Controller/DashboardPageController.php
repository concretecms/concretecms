<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Validation\CSRF\Token;
use Doctrine\ORM\EntityManager;
use Loader;

class DashboardPageController extends PageController
{
    protected $error;

    protected $restrictedMethods = array(
        'enableNativeMobile',
        'getEntityManager'
    );

    /** @var Token */
    public $token;
    protected $helpers = array('form');

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
        $this->token = Loader::helper('validation/token');
        $this->error = Loader::helper('validation/error');
        $this->set('interface', Loader::helper('concrete/ui'));
        $this->set('dashboard', Loader::helper('concrete/dashboard'));

        $this->entityManager = \Core::make('Doctrine\ORM\EntityManager');

        $hideDashboardPanel = false;
        if (\Cookie::has('dashboardPanelStatus') && \Cookie::get('dashboardPanelStatus') == 'closed') {
            $hideDashboardPanel = true;
        }
        $this->set('hideDashboardPanel', $hideDashboardPanel);
        \Core::make('helper/concrete/dashboard');
        $dh = \Concrete\Core\Application\Service\DashboardMenu::getMine();
        if ($dh->contains($this->getPageObject())) {
            $this->set("_bookmarked", true);
        } else {
            $this->set('_bookmarked', false);
        }
    }

    public function on_before_render()
    {
        $pageTitle = $this->get('pageTitle');
        if (!$pageTitle) {
            $this->set('pageTitle', $this->c->getCollectionName());
        }
        $this->set('token', $this->token);
        $this->set('error', $this->error);
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Controller\PageController::useUserLocale()
     */
    public function useUserLocale()
    {
        return true;
    }
}
