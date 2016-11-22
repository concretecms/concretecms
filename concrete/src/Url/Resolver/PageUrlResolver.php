<?php
namespace Concrete\Core\Url\Resolver;

use Concrete\Core\Url\Url;

class PageUrlResolver implements UrlResolverInterface
{
    /** @var UrlResolverInterface */
    protected $pathUrlResolver;

    public function __construct(PathUrlResolver $path_url_resolver)
    {
        $this->pathUrlResolver = $path_url_resolver;
    }

    public function resolve(array $arguments, $resolved = null)
    {
        if ($resolved) {
            // We don't need to do any post processing on urls.
            return $resolved;
        }

        if ($arguments) {
            $page = head($arguments);
        }

        if (isset($page) && $page instanceof \Concrete\Core\Page\Page) {

            if ($externalUrl = $page->getCollectionPointerExternalLink()) {
                return $externalUrl;
            }

            if ($path = $page->getCollectionPath()) {
                return $this->resolveWithResolver($path, $arguments);
            }

            // if there's no path but it's the home page
            if ($page->isHomePage()) {
                return $this->resolveWithResolver("/", $arguments);
            }

            // otherwise, it's a page object with no path yet, which happens when pages aren't yet approved
            return $this->resolveWithResolver('/?cID=' . $page->getCollectionID(), $arguments);
        }

        return null;
    }

    protected function resolveWithResolver($path, $arguments)
    {
        array_unshift($arguments, $path);

        return $this->pathUrlResolver->resolve($arguments);
    }
}
