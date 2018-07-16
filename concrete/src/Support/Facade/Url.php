<?php
namespace Concrete\Core\Support\Facade;

class Url extends Facade
{
    /**
     * @return \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface
     */
    public static function getFacadeRoot()
    {
        return parent::getFacadeRoot();
    }

    public static function getFacadeAccessor()
    {
        return 'url/manager';
    }

    /**
     * Resolve a URL from data.
     *
     * Working core examples for example.com:
     * \Url::to('/some/path', 'some_action', $some_variable = 2)
     *     http://example.com/some/path/some_action/2/
     *
     * \Url::to($page_object = \Page::getByPath('blog'), 'action')
     *     http://example.com/blog/action/
     *
     * @return \League\URL\URLInterface
     */
    public static function to(/* ... */)
    {
        return static::getFacadeRoot()->resolve(func_get_args());
    }

    /**
     * This method is only here as a legacy decorator, use url::to.
     *
     * @return \League\URL\URLInterface
     *
     * @deprecated
     */
    public static function route($data)
    {
        $arguments = array_slice(func_get_args(), 1);
        if (!$arguments) {
            $arguments = array();
        }
        $route = static::getFacadeApplication()->make(\Router::class)->route($data);
        array_unshift($arguments, $route);

        return static::getFacadeRoot()->resolve($arguments);
    }

    /**
     * This method is only here as a legacy decorator, use `\URL::to($page)`.
     *
     * @return \League\URL\URLInterface
     *
     * @deprecated
     */
    public static function page()
    {
        return static::getFacadeRoot()->resolve(func_get_args());
    }
}
