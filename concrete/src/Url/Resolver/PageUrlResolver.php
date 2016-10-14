<?php
namespace Concrete\Core\Url\Resolver;

use Concrete\Core\Entity\Site\SiteTree;
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

        $page = array_shift($arguments);
        if ($page && $page instanceof \Concrete\Core\Page\Page) {

            if ($externalUrl = $page->getCollectionPointerExternalLink()) {
                return Url::createFromUrl($externalUrl);
            }

            $site = null;
            $tree = $page->getSiteTreeObject();
            if ($tree instanceof SiteTree) {
                $site = $tree->getSite();
            }

            if ($path = $page->getCollectionPath()) {
                return $this->resolveWithResolver($path, $arguments, $site);
            }

            // if there's no path but it's the home page
            if ($page->getCollectionID() == HOME_CID) {
                return $this->resolveWithResolver("/", $arguments, $site);
            }

            // otherwise, it's a page object with no path yet, which happens when pages aren't yet approved
            return $this->resolveWithResolver('/?cID=' . $page->getCollectionID(), $arguments, $site);
        }

        return null;
    }

    protected function resolveWithResolver($path, $arguments, $site = null)
    {
        array_unshift($arguments, $path);

        return $this->pathUrlResolver->resolve($arguments);
    }
}
