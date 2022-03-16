<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Foundation\Environment;
use Concrete\Core\Html\Service\Html;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Page\View\PageView;
use Symfony\Component\HttpFoundation\Response;
use Concrete\Core\Session\SessionValidator;

class PageController extends Controller
{
    protected $supportsPageCache = false;
    protected $action;
    protected $passThruBlocks = array();
    protected $parameters = array();
    protected $replacement = null;
    protected $requestValidated;
    /** @var BlockController[] */
    protected $blocks = [];

    /** @var bool A flag to track whether we've loaded sets from the session flash bags */
    private $hasCheckedSessionMessages = false;

    /**
     * array of method names that can't be called through the url
     * @var array
     */
    protected $restrictedMethods = array();

    /**
     * Custom request path - overrides Request::getPath() (useful when replacing controllers).
     * @var string|null
     */
    protected $customRequestPath = null;

    /** @var \Concrete\Core\Page\Page The current page */
    public $c;

    public function supportsPageCache()
    {
        return $this->supportsPageCache;
    }

    public function __construct(Page $c)
    {
        parent::__construct();
        $this->c = $c;
        $this->view = new PageView($this->c);
        $this->set('html', Application::getFacadeApplication()->make(HTML::class));
    }

    /**
     * Given either a path or a Page object, this is a shortcut to
     * 1. Grab the controller of THAT page.
     * 2. Grab the view of THAT controller
     * 3. Render that view.
     * 4. Exit – so we immediately stop all other output in the controller that
     * called render().
     *
     * @param @string|\Concrete\Core\Page\Page $var
     */
    public function replace($var)
    {
        if ($var instanceof Page) {
            $page = $var;
            $path = $var->getCollectionPath();
        } else {
            $path = (string) $var;
            $page = Page::getByPath($path);
        }

        $request = Request::getInstance();
        $controller = $page->getPageController();
        $request->setCurrentPage($page);
        if (is_callable([$controller, 'setCustomRequestPath'])) {
            $controller->setCustomRequestPath($path);
        }
        $this->replacement = $controller;
    }

    /**
     * Set the custom request path (useful when replacing controllers).
     *
     * @param string|null $requestPath Set to null to use the default request path
     */
    public function setCustomRequestPath($requestPath)
    {
        $this->customRequestPath = ($requestPath === null) ? null : (string) $requestPath;
    }

    /**
     * Get the custom request path (useful when replacing controllers).
     *
     * @return string|null Returns null if no custom request path, a string otherwise
     */
    public function getCustomRequestPath()
    {
        return $this->customRequestPath;
    }

    public function isReplaced()
    {
        return !!$this->replacement;
    }

    public function getReplacement()
    {
        return $this->replacement;
    }

    /**
     * Get the things "set" against this controller with `$this->set(...)`
     * This output array may also contain items set with `$this->flash(...)` like `message` `error` `success` or other
     * custom keys
     *
     * @return array Associative array of things set against this controller
     */
    public function getSets()
    {
        // Check if we've already looked at the session flashbag, if so just move on
        if ($this->hasCheckedSessionMessages === false) {
            $this->hasCheckedSessionMessages = true;
            $app = $this->app;
            $validator = $app->make(SessionValidator::class);
            $session = Application::getFacadeApplication()->make('session');

            // Check if we have an active session and our expected flash message
            if ($validator->hasActiveSession() && $session->getFlashBag()->has('page_message')) {
                $value = $session->getFlashBag()->get('page_message');

                // Add each page_message item to the sets for the page
                foreach ($value as $message) {
                    $this->set($message[0], $message[1]);

                    // Also set a `{$key}IsHTML` helper boolean to tell whether the set value is supposed to be HTML
                    $this->set($message[0] . 'IsHTML', isset($message[2]) && $message[2]);
                }
            }
        }

        return parent::getSets();
    }

    /**
     * Given a path to a single page, this command uses the CURRENT controller and renders
     * the contents of the single page within this request. The current controller is not
     * replaced, and has already fired (since it is meant to be called from within a view() or
     * similar method).
     *
     * @param @string
     */
    public function render($path, $pkgHandle = null)
    {
        $view = $this->getViewObject();

        $env = Environment::get();
        $path = trim($path, '/');
        $a = $path . '/' . FILENAME_COLLECTION_VIEW;
        $b = $path . '.php';

        $r = $env->getRecord(DIRNAME_PAGES . '/' . $a);

        if ($r->exists()) {
            $view->renderSinglePageByFilename($a, $pkgHandle);
        } else {
            $view->renderSinglePageByFilename($b, $pkgHandle);
        }
    }

    public function getPageObject()
    {
        return $this->c;
    }

    public function getTheme()
    {
        if ($this->theme === null) {
            $theme = parent::getTheme();
            if (!$theme) {
                $theme = $this->c->getCollectionThemeObject();
                if (is_object($theme)) {
                    $this->theme = $theme->getThemeHandle();
                }
            } else {
                $this->theme = $theme;
            }
        }

        return $this->theme;
    }

    public function getRequestAction()
    {
        return $this->action;
    }

    public function getRequestActionParameters()
    {
        return $this->parameters;
    }

    public function getControllerActionPath()
    {
        if (isset($this->controllerActionPath)) {
            return $this->controllerActionPath;
        }

        if (is_object($this->view)) {
            return $this->view->getViewPath();
        }
    }

    public function setupRequestActionAndParameters(Request $request)
    {
        $requestPath = $this->getCustomRequestPath();
        if ($requestPath === null) {
            $requestPath = $request->getPath();
        }

        if (!empty($this->c->getCollectionPath()) && stripos($requestPath, $this->c->getCollectionPath()) !== false) {
            // If the request path starts with the collection path, remove it
            $task = substr($requestPath, strlen($this->c->getCollectionPath()) + 1);
        } else {
            // Otherwise, just remove leading slash
            $task = ltrim($requestPath, '/');
        }
        $task = str_replace('-/', '', $task);
        $taskparts = explode('/', $task);
        if (isset($taskparts[0]) && $taskparts[0] !== '') {
            $method = $taskparts[0];
        } elseif (is_object($this->c) && is_callable(array($this, $this->c->getCollectionHandle()))) {
            $method = $this->c->getCollectionHandle();
        } else {
            $method = 'view';
        }

        $foundTask = false;
        $restrictedControllers = array(
            'Concrete\Core\Controller\Controller',
            'Concrete\Core\Controller\AbstractController',
            'Concrete\Core\Page\Controller\PageController'

        );
        try {
            $r = new \ReflectionMethod(get_class($this), $method);
            $cl = $r->getDeclaringClass();
            if (is_object($cl)) {
                if (
                    !in_array($cl->getName(), $restrictedControllers)
                    && strpos($method, 'on_') !== 0
                    && strpos($method, '__') !== 0
                    && $r->isPublic()
                    && !$r->isConstructor()
                    && (is_array($this->restrictedMethods) && !in_array($method, $this->restrictedMethods))
                ) {
                    $foundTask = true;
                }
            }
        } catch (\Exception $e) {
        }

        if ($foundTask) {
            $this->action = $method;
            if (isset($taskparts[1])) {
                array_shift($taskparts);
                $this->parameters = $taskparts;
            }
        } else {
            $this->action = 'view';
            if ($taskparts[0] !== '') {
                $this->parameters = $taskparts;
            }
        }
    }

    public function isValidControllerTask($action, $parameters = array())
    {
        $valid = true;
        if (!is_callable(array($this, $this->action)) && count($this->parameters) > 0) {
            $valid = false;
        }

        if ($valid && is_callable(array($this, $this->action))  && !($this instanceof \Concrete\Controller\SinglePage\PageForbidden)) {
            // we use reflection to see if the task itself, which now much exist, takes fewer arguments than
            // what is specified
            $r = new \ReflectionMethod(get_class($this), $this->action);
            if ($r->getNumberOfParameters() < count($this->parameters)) {
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * @param Block $b
     * @param BlockController $controller
     */
    public function setPassThruBlockController(Block $b, BlockController $controller)
    {
        $this->passThruBlocks[$b->getBlockID()] = $controller;
    }

    public function getPassThruBlockController(Block $b)
    {
        $bID = $b->getBlockID();

        return isset($this->passThruBlocks[$bID]) ? $this->passThruBlocks[$bID] : null;
    }

    /**
     * @since 9.0.3
     * @param Block $block
     * @param BlockController $controller
     * @return void
     */
    public function setBlockController(Block $block, BlockController $controller)
    {
        $this->blocks[$block->getBlockID()] = $controller;
    }

    /**
     * @since 9.0.3
     * @param Block $block
     * @return BlockController|null
     */
    public function getBlockController(Block $block): ?BlockController
    {
        $bID = $block->getBlockID();

        return $this->blocks[$bID] ?? null;
    }

    public function validateRequest()
    {

        if (isset($this->requestValidated)) {
            return $this->requestValidated;
        }

        $valid = true;

        $blockControllers = [];
        $blocks = array_merge($this->getPageObject()->getBlocks(), $this->getPageObject()->getGlobalBlocks());
        foreach ($blocks as $block) {
            $controller = $block->getController();
            // We have to run on_start() method of all blocks on this page instead of only ones that have valid tasks
            $controller->on_start();
            // We'll also reuse this controller in BlockView, so let's store it to avoid duplicate calls
            $this->setBlockController($block, $controller);
            $blockControllers[] = $controller;
        }

        if (!$this->isValidControllerTask($this->action, $this->parameters)) {
            $valid = false;
            // we check the blocks on the page.
            foreach ($blockControllers as $controller) {
                list($method, $parameters) = $controller->getPassThruActionAndParameters($this->parameters);
                if ($controller->isValidControllerTask($method, $parameters)) {
                    $response = $controller->runAction($method, $parameters);
                    if ($response instanceof Response) {
                        return $response;
                    }
                    // old school blocks have already terminated at this point. They are redirecting
                    // or exiting. But new blocks like topics, etc... can actually rely on their $set
                    // data persisting and being passed into the view.

                    // so if we make it down here we have to return true –so that we don't fire a 404.
                    $valid = true;

                    // then, we need to save the persisted data that may have been set.
                    $controller->setPassThruBlockController($this);
                }
            }

            if (!$valid) {
                // finally, we check additional page paths.
                $paths = $this->getPageObject()->getAdditionalPagePaths();
                foreach ($paths as $path) {
                    if ($path->getPagePath() == $this->request->getPath()) {
                        // This is an additional page path to a page. We 301 redirect.
                        return Redirect::page($this->getPageObject(), 301);
                    }
                }
            }
        }

        $this->requestValidated = $valid;

        return $valid;
    }

    /**
     * Should this page be displayed using the user's language?
     *
     * @return bool
     */
    public function useUserLocale()
    {
        return false;
    }

    /**
     * Override this method to send content created by the page controller to the indexer
     */
    public function getSearchableContent()
    {
        return;
    }

    /**
     * Build a Redirect Response that instruct the browser to load the first accessible child page of this page.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response Return a RedirectResponse if an accessible child page is found, a forbidden Response otherwise
     */
    public function buildRedirectToFirstAccessibleChildPage()
    {
        $myPage = $this->getPageObject();
        if ($myPage && !$myPage->isError()) {
            $firstChildPage = $myPage->getFirstChild();
            if ($firstChildPage && !$firstChildPage->isError() && (new Checker($firstChildPage))->canRead()) {
                return $this->buildRedirect([$firstChildPage]);
            }
            foreach ($myPage->getCollectionChildren() as $childPage) {
                if (!$childPage->isError() && (new Checker($childPage))->canRead()) {
                    return $this->buildRedirect([$childPage]);
                }
            }
        }

        return $this->app->make(ResponseFactoryInterface::class)->forbidden($this->request->getUri());
    }
}
