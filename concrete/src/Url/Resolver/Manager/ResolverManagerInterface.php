<?php
namespace Concrete\Core\Url\Resolver\Manager;

use Concrete\Core\Url\Resolver\UrlResolverInterface;

interface ResolverManagerInterface
{
    /**
     * @return UrlResolverInterface|null
     */
    public function getDefaultResolver();

    /**
     * @param string $handle
     *
     * @return UrlResolverInterface|null
     */
    public function getResolver($handle);

    /**
     * @param string               $handle
     * @param UrlResolverInterface $resolver
     * @param int                  $priority The order in which we ask for a url, 1 is first, 1024 is last.
     */
    public function addResolver(
        $handle,
        UrlResolverInterface $resolver,
        $priority = 512
    );

    /**
     * Resolve a URI.
     *
     * @param array $args This can be an array of any information.
     *
     * @return \League\URL\URLInterface
     */
    public function resolve(array $args);
}
