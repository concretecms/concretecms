<?php

namespace Concrete\Core\Url\Resolver;

class CallableUrlResolver implements UrlResolverInterface
{
    protected $resolver;

    /**
     * @param callable $resolver
     *
     * @see \Concrete\Core\Url\Resolver\CallableUrlResolver::setResolver() for a description of $resolver
     */
    public function __construct($resolver)
    {
        $this->setResolver($resolver);
    }

    /**
     * @param callable $resolver A Callable that receives three arguments
     *                               CallableUrlResolver $resolver,
     *                               array               $arguments,
     *                               string|null         $resolved
     */
    public function setResolver(/* callable */ $resolver)
    {
        if (!is_callable($resolver)) {
            throw new \InvalidArgumentException(
                'Resolver not callable');
        }

        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Url\Resolver\UrlResolverInterface::resolve()
     */
    public function resolve(array $arguments, $resolved = null)
    {
        return $this->resolver ? call_user_func($this->resolver, $this, $arguments, $resolved) : null;
    }
}
