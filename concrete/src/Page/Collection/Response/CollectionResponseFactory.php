<?php

namespace Concrete\Core\Page\Collection\Response;

use Concrete\Controller\Frontend\PageForbidden;
use Concrete\Controller\Frontend\PageNotFound;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Package\ItemCategory\PermissionKey;
use Concrete\Core\Page\Collection\Version\Version;
use Concrete\Core\Page\Event;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Routing\RouterInterface;
use Concrete\Core\User\User;
use Concrete\Core\View\View;
use Symfony\Component\HttpFoundation\Session\Session;
use Concrete\Core\Page\Collection\Collection;

class CollectionResponseFactory extends ResponseFactory implements ResponseFactoryInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var \Concrete\Core\Localization\Localization
     */
    protected $localization;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    public function __construct(Localization $localization, Session $session, Repository $config, Request $request)
    {
        $this->localization = $localization;
        $this->session = $session;
        $this->config = $config;
        $this->request = $request;
    }

    protected function createPageNotFound(Collection $collection, Request $request)
    {
        $item = '/page_not_found';
        $c = Page::getByPath($item);
        if (is_object($c) && !$c->isError()) {
            $item = $c;
            $request->setCurrentPage($c);
            $cnt = $item->getPageController();
        } else {
            $cnt = $this->app->make(PageNotFound::class);
        }

        $this->controller($cnt, 404);
    }

}
