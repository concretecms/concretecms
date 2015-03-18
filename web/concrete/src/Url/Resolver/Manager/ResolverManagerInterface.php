<?php
namespace Concrete\Core\Url\Resolver\Manager;

use Concrete\Core\Url\Resolver\UrlResolverInterface;

interface ResolverManagerInterface
{

    /**
     * @return URLResolverInterface|null
     */
    public function getDefaultResolver();

    /**
     * @param string $handle
     * @return URLResolverInterface|null
     */
    public function getResolver($handle);

    /**
     * @param string               $handle
     * @param URLResolverInterface $resolver
     * @param int                  $priority The order in which we ask for a url, 1 is first, 1024 is last.
     * @return void
     */
    public function addResolver(
        $handle,
        URLResolverInterface $resolver,
        $priority = 512
    );

    /**
     * Resolve a URI
     *
     * @param array $args This can be an array of any information.
     * @return \League\URL\URLInterface
     */
    public function resolve(array $args);

}
