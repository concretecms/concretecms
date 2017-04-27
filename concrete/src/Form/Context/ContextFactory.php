<?php
namespace Concrete\Core\Form\Context;

class ContextFactory
{

    protected $provider;

    public function __construct(ContextProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function getContext(ContextInterface $context)
    {
        $registry = $this->provider->getContextRegistry();
        if ($registry) {
            $class = get_class($context);
            foreach($registry->getContexts() as $contextClass => $contextToReturn) {
                if ($class == $contextClass) {
                    return new $contextToReturn();
                }
            }
        }
        return $context;
    }

}
