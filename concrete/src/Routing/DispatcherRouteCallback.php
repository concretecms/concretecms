<?php
namespace Concrete\Core\Routing;

use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\View\View;
use Page;
use Core;

class DispatcherRouteCallback extends RouteCallback
{
    /**
     * @var \Concrete\Core\Http\ResponseFactoryInterface
     */
    protected $factory;

    /**
     * DispatcherRouteCallback constructor.
     *
     * @param $callback
     * @param $factory
     */
    public function __construct($callback, ResponseFactoryInterface $factory)
    {
        parent::__construct($callback);
        $this->factory = $factory;
    }

    public function execute(Request $request, Route $route = null, $parameters = [])
    {
        // figure out where we need to go
        $c = Page::getFromRequest($request);

        return $this->factory->collection($c);
    }

    public static function getRouteAttributes($callback)
    {
        $callback = Core::make(self::class, [$callback]);

        return ['callback' => $callback];
    }

    /**
     * @deprecated Use CollectionResponseFactory
     *
     * @param \Concrete\Core\View\View $view
     * @param int $code
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendResponse(View $view, $code = 200)
    {
        return $this->factory->view($view, $code);
    }

    /**
     * @deprecated Use CollectionResponseFactory
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendPageNotFound(Request $request)
    {
        return $this->factory->notFound('');
    }

    /**
     * @deprecated Use CollectionResponseFactory
     *
     * @param Request $request
     * @param $currentPage
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function sendPageForbidden(Request $request, $currentPage)
    {
        return $this->factory->forbidden($request->getUri());
    }
}
