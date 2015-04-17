<?php
namespace Concrete\Core\Url\Resolver\Manager;

use Concrete\Core\Url\Resolver\UrlResolverInterface;

class ResolverManager implements ResolverManagerInterface
{

    protected $priorityTree;
    protected $resolvers;
    protected $default;

    /**
     * @param string               $default_handle
     * @param URLResolverInterface $default_resolver
     */
    public function __construct(
        $default_handle,
        URLResolverInterface $default_resolver
    ) {
        $this->addResolver($default_handle, $default_resolver, 1025);
        $this->default = $default_handle;
    }

    /**
     * {@inheritdoc}
     */
    public function addResolver(
        $handle,
        URLResolverInterface $resolver,
        $priority = 512
    ) {
        $priority = min(1024, max(1, intval($priority, 10)));

        $this->resolvers[$handle] = $resolver;

        if (!isset($this->priorityTree[$priority])) {
            $this->priorityTree[$priority] = array();
            ksort($this->priorityTree);
        }
        $this->priorityTree[$priority][] = $handle;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResolver()
    {
        return $this->getResolver($this->default);
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver($handle)
    {
        return array_get($this->resolvers, $handle, null);
    }

    /**
     * @return array
     */
    public function getResolvers()
    {
        return $this->resolvers;
    }

    /**
     * {@inheritdoc}
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

        return $this->getResolver($this->default)->resolve($args, $resolved);
    }

}
