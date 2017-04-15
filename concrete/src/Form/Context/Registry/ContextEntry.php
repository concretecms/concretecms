<?php
namespace Concrete\Core\Form\Context\Registry;

use Concrete\Core\Form\Context\ContextInterface;

class ContextEntry
{

    protected $context;
    protected $contextToReturn;

    public function __construct($context, $contextToReturn)
    {
        $reflectionClass = new \ReflectionClass($context);
        if (!($reflectionClass->implementsInterface(ContextInterface::class))) {
            throw new \RuntimeException(t('%s is not an instance of %s', $context, ContextInterface::class));
        }
        $reflectionClassReturn = new \ReflectionClass($contextToReturn);
        if (!($reflectionClassReturn->implementsInterface(ContextInterface::class))) {
            throw new \RuntimeException(t('%s is not an instance of %s', $context, ContextInterface::class));
        }

        $this->context = $context;
        $this->contextToReturn = $contextToReturn;
    }

    /**
     * @return ContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param ContextInterface $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * @return ContextInterface
     */
    public function getContextToReturn()
    {
        return $this->contextToReturn;
    }

    /**
     * @param ContextInterface $contextToReturn
     */
    public function setContextToReturn($contextToReturn)
    {
        $this->contextToReturn = $contextToReturn;
    }





}
