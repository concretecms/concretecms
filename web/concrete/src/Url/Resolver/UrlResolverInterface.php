<?php
namespace Concrete\Core\Url\Resolver;

interface UrlResolverInterface
{

    /**
     * Resolve url's from any type of input
     *
     * This method MUST either return a `\League\Url\Url` when a url is resolved
     * or null when a url cannot be resolved.
     *
     * @param array                    $arguments A list of the arguments
     * @param \League\Url\UrlInterface $resolved
     * @return \League\Url\UrlInterface
     */
    public function resolve(array $arguments, $resolved = null);

}
