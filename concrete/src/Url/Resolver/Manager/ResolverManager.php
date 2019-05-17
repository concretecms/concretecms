<?php

namespace Concrete\Core\Url\Resolver\Manager;

use Concrete\Core\Url\Resolver\UrlResolverInterface;

class ResolverManager implements ResolverManagerInterface
{
    /**
     * @var string[][]
     */
    protected $priorityTree = [];

    /**
     * @var \Concrete\Core\Url\Resolver\UrlResolverInterface[]
     */
    protected $resolvers;

    /**
     * @var string
     */
    protected $default;

    /**
     * @param string $default_handle
     * @param \Concrete\Core\Url\Resolver\UrlResolverInterface|null $default_resolver
     */
    public function __construct($default_handle = '', UrlResolverInterface $default_resolver = null)
    {
        if ($default_resolver) {
            $this->addResolver($default_handle, $default_resolver);
        }
        $this->default = $default_handle;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface::addResolver()
     */
    public function addResolver($handle, UrlResolverInterface $resolver, $priority = 512)
    {
        $priority = min(1024, max(1, (int) $priority));

        $this->resolvers[$handle] = $resolver;

        if (!isset($this->priorityTree[$priority])) {
            $this->priorityTree[$priority] = [];
            ksort($this->priorityTree);
        }
        $this->priorityTree[$priority][] = $handle;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface::getDefaultResolver()
     */
    public function getDefaultResolver()
    {
        return $this->getResolver($this->default);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface::getResolver()
     */
    public function getResolver($handle)
    {
        return array_get($this->resolvers, $handle, null);
    }

    /**
     * @return \Concrete\Core\Url\Resolver\UrlResolverInterface[]
     */
    public function getResolvers()
    {
        return $this->resolvers;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface::resolve()
     */
    public function resolve(array $args)
    {
        $resolved = null;

        foreach ($this->priorityTree as $list) {
            foreach ($list as $handle) {
                if ($handle == $this->default) {
                    continue;
                }
                $resolver = $this->getResolver($handle);
                $resolved = $resolver->resolve($args, $resolved);
            }
        }

        if ($default = $this->getResolver($this->default)) {
            $resolved = $default->resolve($args, $resolved);
        }

        return $resolved;
    }
}
