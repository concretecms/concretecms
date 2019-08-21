<?php

namespace Concrete\Core\Url\Resolver;

class PageUrlResolver implements UrlResolverInterface
{
    /**
     * @var \Concrete\Core\Url\Resolver\PathUrlResolver
     */
    protected $pathUrlResolver;

    public function __construct(PathUrlResolver $path_url_resolver)
    {
        $this->pathUrlResolver = $path_url_resolver;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Url\Resolver\UrlResolverInterface::resolve()
     */
    public function resolve(array $arguments, $resolved = null)
    {
        if ($resolved) {
            // We don't need to do any post processing on urls.
            return $resolved;
        }

        $page = $arguments ? head($arguments) : null;

        if ($page instanceof \Concrete\Core\Page\Page) {
            if ($externalUrl = $page->getCollectionPointerExternalLink()) {
                return $this->resolveWithResolver($externalUrl, []);
            }

            if ($path = $page->getCollectionPath()) {
                return $this->resolveWithResolver($path, $arguments);
            }

            // if there's no path but it's the home page
            if ($page->isHomePage()) {
                return $this->resolveWithResolver('/', $arguments);
            }

            // otherwise, it's a page object with no path yet, which happens when pages aren't yet approved
            return $this->resolveWithResolver('/?cID=' . $page->getCollectionID(), $arguments);
        }

        return null;
    }

    /**
     * @param string $path
     * @param array $arguments
     *
     * @return \League\URL\URLInterface
     */
    protected function resolveWithResolver($path, $arguments)
    {
        array_unshift($arguments, $path);

        return $this->pathUrlResolver->resolve($arguments);
    }
}
