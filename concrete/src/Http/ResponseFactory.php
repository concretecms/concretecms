<?php

namespace Concrete\Core\Http;

use Concrete\Controller\Frontend\PageForbidden;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Collection\Response\ResponseFactoryInterface as CollectionResponseFactoryInterface;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Page\Event;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\User\User;
use Concrete\Core\View\View;
use Detection\MobileDetect;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class ResponseFactory implements ResponseFactoryInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $session;

    /**
     * @var \Concrete\Core\Http\Request
     */
    protected $request;
    /**
     * @var \Concrete\Core\Http\Localization
     */
    private $localization;
    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    private $config;

    public function __construct(Session $session, Request $request, Localization $localization, Repository $config)
    {
        $this->session = $session;
        $this->request = $request;
        $this->localization = $localization;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function create($content, $code = Response::HTTP_OK, array $headers = array())
    {
        return \Concrete\Core\Http\Response::create($content, $code, $headers);
    }

    /**
     * @inheritdoc
     */
    public function json($data, $code = Response::HTTP_OK, array $headers = array())
    {
        return JsonResponse::create($data, $code, $headers);
    }

    /**
     * @inheritdoc
     */
    public function notFound($content, $code = Response::HTTP_NOT_FOUND, $headers = array())
    {
        if (strcasecmp($this->request->server->get('HTTP_X_REQUESTED_WITH', ''), 'xmlhttprequest') === 0) {
            $loc = $this->localization;
            $changeContext = $this->shouldChangeContext();
            if ($changeContext) {
                $loc->pushActiveContext('site');
            }
            $responseData = [
                'error' => t('Page not found'),
                'errors' => [t('Page not found')],
            ];
            if ($changeContext) {
                $loc->popActiveContext();
            }

            return $this->json($responseData, $code, $headers);
        }

        $item = '/page_not_found';
        $c = Page::getByPath($item);

        if (is_object($c) && !$c->isError()) {
            return $this->collection($c, $code, $headers);
        }

        $cnt = $this->app->make(PageForbidden::class);
        $this->controller($cnt, $code, $headers);
    }

    /**
     * @inheritdoc
     */
    public function error($content, $code = Response::HTTP_INTERNAL_SERVER_ERROR, $headers = array())
    {
        return $this->create($content, $code, $headers);
    }

    /**
     * @inheritdoc
     */
    public function forbidden($requestUrl, $code = Response::HTTP_FORBIDDEN, $headers = array())
    {
        // set page for redirection after successful login
        $this->session->set('rUri', $requestUrl);

        // load page forbidden
        $item = '/page_forbidden';
        $c = Page::getByPath($item);
        if (is_object($c) && !$c->isError()) {
            return $this->collection($c, $code, $headers);
        }

        $cnt = $this->app->make(PageForbidden::class);
        $this->controller($cnt, $code, $headers);
    }

    /**
     * @inheritdoc
     */
    public function redirect($to, $code = Response::HTTP_MOVED_PERMANENTLY, $headers = array())
    {
        return RedirectResponse::create($to, $code, $headers);
    }

    /**
     * @inheritdoc
     */
    public function view(View $view, $code = Response::HTTP_OK, $headers = array())
    {
        $changeContext = $this->shouldChangeContext();
        if ($changeContext) {
            $this->localization->pushActiveContext('site');
        }

        $contents = $view->render();
        if ($changeContext) {
            $this->localization->popActiveContext();
        }

        return $this->create($contents, $code, $headers);
    }

    /**
     * @inheritdoc
     */
    public function controller(Controller $controller, $code = Response::HTTP_OK, $headers = array())
    {
        $request = $this->request;

        $view = $controller->getViewObject();

        // Mobile theme
        if ($this->config->get('concrete.misc.mobile_theme_id') > 0) {
            $md = $this->app->make(MobileDetect::class);
            if ($md->isMobile()) {
                $mobileTheme = Theme::getByID(Config::get('concrete.misc.mobile_theme_id'));
                if ($mobileTheme instanceof Theme) {
                    $view->setViewTheme($mobileTheme);
                    $controller->setTheme($mobileTheme);
                }
            }
        }

        $controller->on_start();

        if ($controller instanceof PageController) {
            $controller->setupRequestActionAndParameters($request);

            $response = $controller->validateRequest();
            // If validaterequest returned a response
            if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
                return $response;
            } else {
                // If validateRequest did not return true
                if ($response == false) {
                    return $this->notFound('', Response::HTTP_NOT_FOUND, $headers);
                }
            }

            $requestTask = $controller->getRequestAction();
            $requestParameters = $controller->getRequestActionParameters();
            $response = $controller->runAction($requestTask, $requestParameters);
            if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
                return $response;
            }

        } else {
            $controller->runAction('view');
        }

        $view->setController($controller);

        if ($controller->isReplaced()) {
            return $this->controller($controller->getReplacement());
        }

        return $this->view($view, $code, $headers);
    }

    /**
     * @inheritdoc
     */
    public function collection(Collection $collection, $code = Response::HTTP_OK, $headers = array())
    {
        if (!$this->app) {
            throw new \RuntimeException('Cannot resolve collections without a reference to the application');
        }

        $request = $this->request;

        if ($collection->isError() && $collection->getError() == COLLECTION_NOT_FOUND) {
            if ($response = $this->collectionNotFound($collection, $request, $headers)) {
                return $response;
            }
        }

        if ($collection->getCollectionPath() == '/page_not_found') {
            return $this->controller($collection->getController());
        }

        if (!isset($collection->cPathFetchIsCanonical) || !$collection->cPathFetchIsCanonical) {
            // Handle redirect URL (additional page paths)
            /** @var Url $url */
            $url = $this->app->make('url/manager')->resolve([$collection]);
            $query = $url->getQuery();
            $query->modify($request->getQueryString());

            $url = $url->setQuery($query);
            return $this->redirect($url, Response::HTTP_MOVED_PERMANENTLY, $headers);
        }

        // maintenance mode
        if ($collection->getCollectionPath() != '/login') {
            $smm = $this->config->get('concrete.maintenance_mode');
            if ($smm == 1 && !PermissionKey::getByHandle('view_in_maintenance_mode')->validate() && ($_SERVER['REQUEST_METHOD'] != 'POST' || Loader::helper('validation/token')->validate() == false)) {
                $v = new View('/frontend/maintenance_mode');

                $router = $this->app->make(RouterInterface::class);
                $tmpTheme = $router->getThemeByRoute('/frontend/maintenance_mode');
                $v->setViewTheme($tmpTheme[0]);
                $v->addScopeItems(['c' => $collection]);
                $request->setCurrentPage($collection);
                if (isset($tmpTheme[1])) {
                    $v->setViewTemplate($tmpTheme[1]);
                }

                return $this->view($v, $code, $headers);
            }
        }

        if ($collection->getCollectionPointerExternalLink() != '') {
            return $this->redirect($collection);
        }

        $cp = new Checker($collection);

        if ($cp->isError() && $cp->getError() == COLLECTION_FORBIDDEN) {
            return $this->forbidden($request->getUri(), Response::HTTP_FORBIDDEN, $headers);
        }

        if (!$collection->isActive() && (!$cp->canViewPageVersions())) {
            return $this->notFound('', Response::HTTP_NOT_FOUND, $headers);
        }

        $scheduledVersion = Version::get($collection, "SCHEDULED");
        if ($publishDate = $scheduledVersion->cvPublishDate) {
            $datetime = $this->app->make('helper/date');
            $now = $datetime->date('Y-m-d G:i:s');

            if (strtotime($now) >= strtotime($publishDate)) {
                $scheduledVersion->approve();
                $collection->loadVersionObject('ACTIVE');
            }
        }

        if ($cp->canEditPageContents() || $cp->canEditPageProperties() || $cp->canViewPageVersions()) {
            $collection->loadVersionObject('RECENT');
        }

        $vp = new Checker($collection->getVersionObject());

        // returns the $vp object, which we then check
        if (is_object($vp) && $vp->isError()) {
            switch ($vp->getError()) {
                case COLLECTION_NOT_FOUND:
                    return $this->notFound('', Response::HTTP_NOT_FOUND, $headers);
                    break;
                case COLLECTION_FORBIDDEN:
                    return $this->forbidden($request->getUri(), Response::HTTP_FORBIDDEN, $headers);
                    break;
            }
        }

        // Now that we've passed all permissions checks, and we have a page, we check to see if we
        // ought to redirect based on base url or trailing slash settings
        $cms = $this->app;
        $site = $this->app['site']->getSite();

        $response = $cms->handleCanonicalURLRedirection($request, $site);
        if (!$response) {
            $response = $cms->handleURLSlashes($request, $site);
        }
        if (isset($response)) {
            return $response;
        }

        // Now we check to see if we're on the home page, and if it multilingual is enabled,
        // and if so, whether we should redirect to the default language page.
        $dl = $cms->make('multilingual/detector');
        if ($dl->isEnabled()) {
            if ($collection->getCollectionID() == $site->getSiteHomePageID() &&
                $site->getConfigRepository()->get('multilingual.redirect_home_to_default_locale')) {
                // Let's retrieve the default language
                $ms = $dl->getPreferredSection();
                if (is_object($ms) && $ms->getCollectionID() != $site->getSiteHomePageID()) {
                    return $this->redirect($ms);
                }
            }

            $dl->setupSiteInterfaceLocalization($collection);
        }

        $request->setCurrentPage($collection);
        $c = $collection; // process.php needs this
        require DIR_BASE_CORE . '/bootstrap/process.php';
        $u = new User();

        // On page view event.
        $pe = new Event($collection);
        $pe->setUser($u);
        $pe->setRequest($request);
        $this->app['director']->dispatch('on_page_view', $pe);

        $controller = $collection->getPageController();

        // we update the current page with the one bound to this controller.
        $collection->setController($controller);

        return $this->controller($controller);
    }

    private function collectionNotFound(Collection $collection, Request $request, array $headers)
    {
        // if we don't have a path and we're doing cID, then this automatically fires a 404.
        if (!$request->getPath() && $request->get('cID')) {
            return $this->notFound('', Response::HTTP_NOT_FOUND, $headers);
        }

        // let's test to see if this is, in fact, the home page,
        // and we're routing arguments onto it (which is screwing up the path.)
        $home = Page::getByID(HOME_CID);
        $request->setCurrentPage($home);
        $homeController = $home->getPageController();
        $homeController->setupRequestActionAndParameters($request);

        $response = $homeController->validateRequest();
        if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
            return $response;
        } else {
            return $this->notFound('', Response::HTTP_NOT_FOUND, $headers);
        }
    }


    /**
     * Check to see if we should change the localization context
     * @return bool
     */
    private function shouldChangeContext()
    {
        $mlEnabled = $this->app->make('multilingual/detector')->isEnabled();
        $inDashboard = $this->app->make('helper/concrete/dashboard')->inDashboard();

        return $mlEnabled && !$inDashboard;
    }

}
