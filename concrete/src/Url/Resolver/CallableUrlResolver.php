<?php
namespace Concrete\Core\Url\Resolver;

class CallableUrlResolver implements UrlResolverInterface
{

    protected $resolver;

    /**
     * @param callable $resolver A Callable that receives three arguments
     *                               CallableUrlResolver $resolver,
     *                               array               $arguments,
     *                               string|null         $resolved
     *
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
     *
     */
    public function setResolver(/** callable */ $resolver)
    {
        if (!is_callable($resolver)) {
            throw new \InvalidArgumentException(
                'Resolver not callable');
        }

        $this->resolver = $resolver;
    }

    public function resolve(array $arguments, $resolved = null)
    {
        if ($this->resolver) {
            return call_user_func($this->resolver, $this, $arguments, $resolved);
        }
        return null; // @codeCoverageIgnore
    }

}
