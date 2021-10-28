<?php

namespace Concrete\Core\Page\Controller;

use Concrete\Core\Application\Service\DashboardMenu;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\FavoritesNavigationFactory;
use Concrete\Core\Cookie\CookieJar;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Navigation\Breadcrumb\BreadcrumbInterface;
use Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardBreadcrumbFactory;
use Concrete\Core\Navigation\Item\PageItem;
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
     * Useful for traversing the site for elements and their associated controls, while allowing for overriding.
     *
     * @var ElementManager
     */
    protected $elementManager;

    /**
     * @var BreadcrumbInterface
     */
    protected $breadcrumb;

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
        $this->elementManager = $this->app->make(ElementManager::class);
        $this->set('interface', $this->app->make('helper/concrete/ui'));
        $this->set('dashboard', $this->app->make('helper/concrete/dashboard'));
        $this->set('hideDashboardPanel', $cookieJar->get('dashboardPanelStatus') === 'closed');

        $favorites = $this->app->make(FavoritesNavigationFactory::class)->createNavigation();
        if ($favorites->has(new PageItem($this->getPageObject()))) {
            $this->set('_bookmarked', true);
        } else {
            $this->set('_bookmarked', false);
        }
    }

    /**
     * @return mixed
     */
    protected function createBreadcrumbFactory()
    {
        return $this->app->make(DashboardBreadcrumbFactory::class);
    }

    protected function createBreadcrumb() : BreadcrumbInterface
    {
        $factory = $this->createBreadcrumbFactory();
        return $factory->getBreadcrumb($this->getPageObject());
    }

    /**
     * @return BreadcrumbInterface
     */
    protected function getBreadcrumb(): ?BreadcrumbInterface
    {
        return $this->breadcrumb;
    }

    protected function setBreadcrumb(BreadcrumbInterface $breadcrumb)
    {
        $this->breadcrumb = $breadcrumb;
    }

    protected function getBreadcrumbElement()
    {
        return 'dashboard/navigation/breadcrumb';
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
            $this->set('pageTitle', t($this->c->getCollectionName()));
        }
        $breadcrumb = $this->getBreadcrumb();
        if (!$breadcrumb) {
            $breadcrumb = $this->createBreadcrumb();
        }
        $_breadcrumb = $this->elementManager->get($this->getBreadcrumbElement(), [
            'breadcrumb' => $breadcrumb
        ]);

        $dbConfig = $this->app->make('config/database');
        $this->set('showPrivacyPolicyNotice', !$dbConfig->get('app.privacy_policy_accepted'));
        $this->set('token', $this->token);
        $this->set('error', $this->error);
        $this->set('_breadcrumb', $_breadcrumb);
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
