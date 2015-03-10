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

            // if there's no path but it's the home page
            if ($page->getCollectionID() == HOME_CID) {
                return \URL::to('/', $arguments);
            }

            // otherwise, it's a page object with no path yet, which happens when pages aren't yet approved
            // @TODO add code here that handles this.

        }

        return null;
    }

}
