<?php
namespace Concrete\Core\Url\Resolver;

class PageUrlResolver implements UrlResolverInterface
{

    public function resolve(array $arguments, $resolved = null)
    {
        if ($resolved) {
            // We don't need to do any post processing on urls.
            return $resolved;
        }

        $page = array_shift($arguments);
        if ($page && $page instanceof \Concrete\Core\Page\Page) {
            if ($path = $page->getCollectionPath()) {
                return \URL::to(\Core::make('helper/text')->encodePath($path), $arguments);
            }
        }

        return null;
    }

}
