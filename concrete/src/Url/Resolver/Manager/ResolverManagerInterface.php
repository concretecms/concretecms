<?php

namespace Concrete\Core\Url\Resolver\Manager;

use Concrete\Core\Url\Resolver\UrlResolverInterface;

interface ResolverManagerInterface
{
    /**
     * @return \Concrete\Core\Url\Resolver\UrlResolverInterface|null
     */
    public function getDefaultResolver();

    /**
     * @param string $handle
     *
     * @return \Concrete\Core\Url\Resolver\UrlResolverInterface|null
     */
    public function getResolver($handle);

    /**
     * @param string $handle
     * @param \Concrete\Core\Url\Resolver\UrlResolverInterface $resolver
     * @param int $priority the order in which we ask for a url, 1 is first, 1024 is last
     */
    public function addResolver($handle, UrlResolverInterface $resolver, $priority = 512);

    /**
     * Resolve a URI.
     *
     * @param array $args this can be an array of any information
     *
     * @return \League\URL\URLInterface
     */
    public function resolve(array $args);
}
