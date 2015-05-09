<?php
namespace Concrete\Core\Routing;

use \Concrete\Core\Page\Event as PageEvent;
use Concrete\Core\Page\Theme\Theme;
use PermissionKey;
use Request;
use User;
use Events;
use Loader;
use Page;
use Config;
use View;
use Permissions;
use Response;
use Core;
use Session;

class DispatcherRouteCallback extends RouteCallback
{

    protected function sendResponse(View $v, $code = 200)
    {
        $contents = $v->render();
        $response = new Response($contents, $code);

        return $response;
    }

    protected function sendPageNotFound(Request $request)
    {
        $item = '/page_not_found';
        $c = Page::getByPath($item);
        if (is_object($c) && !$c->isError()) {
            $item = $c;
            $request->setCurrentPage($c);
            $cnt = $item->getPageController();
        } else {
            $cnt = Core::make('\Concrete\Controller\Frontend\PageNotFound');
        }

        $v = $cnt->getViewObject();
        $cnt->on_start();
        $cnt->runAction('view');
        $v->setController($cnt);

        return $this->sendResponse($v, 404);
    }

    protected function sendPageForbidden(Request $request, $currentPage)
    {
        // set page for redirection after successful login
        Session::set('rcID', $currentPage->getCollectionID());

        // load page forbidden
        $item = '/page_forbidden';
        $c = Page::getByPath($item);
        if (is_object($c) && !$c->isError()) {
            $item = $c;
            $request->setCurrentPage($c);
            $cnt = $item->getPageController();
        } else {
            $cnt = Core::make('\Concrete\Controller\Frontend\PageForbidden');
        }
        $v = $cnt->getViewObject();
        $cnt->on_start();
        $cnt->runAction('view');
        $v->setController($cnt);

        return $this->sendResponse($v, 403);
    }

    public function execute(Request $request, \Concrete\Core\Routing\Route $route = null, $parameters = array())
    {
        // figure out where we need to go
        $c = Page::getFromRequest($request);
        if ($c->isError() && $c->getError() == COLLECTION_NOT_FOUND) {
            // if we don't have a path and we're doing cID, then this automatically fires a 404.
            if (!$request->getPath() && $request->get('cID')) {
                return $this->sendPageNotFound($request);
            }
            // let's test to see if this is, in fact, the home page,
            // and we're routing arguments onto it (which is screwing up the path.)
            $home = Page::getByID(HOME_CID);
            $request->setCurrentPage($home);
            $homeController = $home->getPageController();
            $homeController->setupRequestActionAndParameters($request);
            if (!$homeController->validateRequest()) {
                return $this->sendPageNotFound($request);
            } else {
                $c = $home;
                $c->cPathFetchIsCanonical = true;
            }
        }
        if (!$c->cPathFetchIsCanonical) {
            // Handle redirect URL (additional page paths)
            return Redirect::page($c, 301)->send();
        }

        // maintenance mode
        if ($c->getCollectionPath() != '/login') {
            $smm = Config::get('concrete.maintenance_mode');
            if ($smm == 1 && !PermissionKey::getByHandle('view_in_maintenance_mode')->validate() && ($_SERVER['REQUEST_METHOD'] != 'POST' || Loader::helper('validation/token')->validate() == false)) {
                $v = new View('/frontend/maintenance_mode');
                $v->setViewTheme(VIEW_CORE_THEME);

                return $this->sendResponse($v);
            }
        }

        if ($c->getCollectionPointerExternalLink() != '') {
            return Redirect::url($c->getCollectionPointerExternalLink(), 301)->send();
        }

        $cp = new Permissions($c);

        if ($cp->isError() && $cp->getError() == COLLECTION_FORBIDDEN) {
            return $this->sendPageForbidden($request, $c);
        }

        if (!$c->isActive() && (!$cp->canViewPageVersions())) {
            return $this->sendPageNotFound($request);
        }

        if ($cp->canEditPageContents() || $cp->canEditPageProperties() || $cp->canViewPageVersions()) {
            $c->loadVersionObject('RECENT');
        }

        $vp = new Permissions($c->getVersionObject());

        // returns the $vp object, which we then check
        if (is_object($vp) && $vp->isError()) {
            switch ($vp->getError()) {
                case COLLECTION_NOT_FOUND:
                    return $this->sendPageNotFound($request);
                    break;
                case COLLECTION_FORBIDDEN:
                    return $this->sendPageForbidden($request, $c);
                    break;
            }
        }

        // Now that we've passed all permissions checks, and we have a page, we check to see if we
        // ought to redirect based on base url or trailing slash settings
        $cms = \Core::make("app");
        $response = $cms->handleCanonicalURLRedirection($request);
        if (!$response) {
            $response = $cms->handleURLSlashes($request);
        }
        if (isset($response)) {
            $response->send();
            exit;
        }

        // Now we check to see if we're on the home page, and if it multilingual is enabled,
        // and if so, whether we should redirect to the default language page.
        if (Config::get('concrete.multilingual.enabled')) {
            $dl = Core::make('multilingual/detector');
            if ($c->getCollectionID() == HOME_CID && Config::get('concrete.multilingual.redirect_home_to_default_locale')) {
                // Let's retrieve the default language
                $ms = $dl->getPreferredSection();
                if (is_object($ms) && $ms->getCollectionID() != HOME_CID) {
                    Redirect::page($ms)->send();
                    exit;
                }
            }

            $dl->setupSiteInterfaceLocalization($c);
        }

        $request->setCurrentPage($c);
        require DIR_BASE_CORE . '/bootstrap/process.php';
        $u = new User();

        // On page view event.
        $pe = new PageEvent($c);
        $pe->setUser($u);
        $pe->setRequest($request);
        Events::dispatch('on_page_view', $pe);

        $controller = $c->getPageController();
        $controller->on_start();
        $controller->setupRequestActionAndParameters($request);
        $response = $controller->validateRequest();
        if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
            return $response;
        } else {
            if ($response == false) {
                return $this->sendPageNotFound($request);
            }
        }
        $requestTask = $controller->getRequestAction();
        $requestParameters = $controller->getRequestActionParameters();
        $controller->runAction($requestTask, $requestParameters);

        $c->setController($controller);
        $view = $controller->getViewObject();

        // Mobile theme
        if (Config::get('concrete.misc.mobile_theme_id') > 0) {
            $md = new \Mobile_Detect();
            if ($md->isMobile()) {
                $mobileTheme = Theme::getByID(Config::get('concrete.misc.mobile_theme_id'));
                if ($mobileTheme instanceof Theme) {
                    $view->setViewTheme($mobileTheme);
                }
            }
        }

        // we update the current page with the one bound to this controller.
        $request->setCurrentPage($c);

        return $this->sendResponse($view);
    }

    public static function getRouteAttributes($callback)
    {
        $callback = new DispatcherRouteCallback($callback);

        return array('callback' => $callback);
    }
}
