<?php
namespace Concrete\Core\Url\Resolver;

class PageUrlResolver implements UrlResolverInterface
{

    public function resolve(array $arguments, $resolved = null)
    {
        $page = array_shift($arguments);

        if ($page && $page instanceof \Page) {
            if ($path = $page->getCollectionPath()) {
                return \URL::to(\Core::make('helper/text')->encodePath($path), $arguments);
            }
        }

        return null;
    }

}
