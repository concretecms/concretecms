<?php

namespace Concrete\Core\Page\Controller;

use Concrete\Core\Application\Service\DashboardMenu;
use Concrete\Core\Cookie\CookieJar;
use Doctrine\ORM\EntityManagerInterface;
use Mobile_Detect;

class DashboardPageController extends PageController
{
    /**
     * The Token instance (available after the on_start method has been called).
     *
     * @var \Concrete\Core\Validation\CSRF\Token|null
     */
    public $token;

    /**
     * The ErrorList instance (available after the on_start method has been called).
     *
     * @var \Concrete\Core\Error\ErrorList\ErrorList|null
     */
    protected $error;

    /**
     * The EntityManager instance (available after the on_start method has been called).
     *
     * @var \Doctrine\ORM\EntityManagerInterface|null
     */
    protected $entityManager;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Controller\PageController::$restrictedMethods
     */
    protected $restrictedMethods = [
        'enableNativeMobile',
        'getEntityManager',
    ];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::$helpers
     */
    protected $helpers = ['form'];

    /**
     * Check if the current user is using a mobile device: if so, configure the dashboard page accordingly.
     */
    public function enableNativeMobile()
    {
        $md = new Mobile_Detect();
        if ($md->isMobile()) {
            $this->addHeaderItem('<meta name="viewport" content="width=device-width,initial-scale=1"/>');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::on_start()
     */
    public function on_start()
    {
        $cookieJar = $this->app->make(CookieJar::class);
        $this->token = $this->app->make('token');
        $this->error = $this->app->make('error');
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
        $this->set('interface', $this->app->make('helper/concrete/ui'));
        $this->set('dashboard', $this->app->make('helper/concrete/dashboard'));
        $this->set('hideDashboardPanel', $cookieJar->get('dashboardPanelStatus') === 'closed');
        // @todo fix this approach
        $this->app->make('helper/concrete/dashboard');
        $dh = DashboardMenu::getMine();
        if ($dh->contains($this->getPageObject())) {
            $this->set('_bookmarked', true);
        } else {
            $this->set('_bookmarked', false);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\AbstractController::on_before_render()
     */
    public function on_before_render()
    {
        $pageTitle = $this->get('pageTitle');
        if (!$pageTitle) {
            $this->set('pageTitle', $this->c->getCollectionName());
        }
        $dbConfig = $this->app->make('config/database');
        $this->set('showPrivacyPolicyNotice', !$dbConfig->get('app.privacy_policy_accepted'));
        $this->set('token', $this->token);
        $this->set('error', $this->error);
    }

    /**
     * Get the EntityManager instance (available after the on_start method has been called).
     *
     * @return \Doctrine\ORM\EntityManagerInterface|null
     */
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
