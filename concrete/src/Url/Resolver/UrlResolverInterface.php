<?php

namespace Concrete\Core\Url\Resolver;

interface UrlResolverInterface
{
    /**
     * Resolve url's from any type of input.
     *
     * This method MUST either return a `\League\Url\UrlInterface` when a url is resolved
     * or null when a url cannot be resolved.
     *
     * @param array $arguments A list of the arguments
     * @param \League\Url\UrlInterface|null $resolved
     *
     * @return \League\Url\UrlInterface|null
     */
    public function resolve(array $arguments, $resolved = null);
}
