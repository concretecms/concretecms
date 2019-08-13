<?php
namespace Concrete\Core\Form\Context\Registry;

/**
 * @since 8.2.0
 */
class ContextRegistry
{

    protected $contexts = array();

    /**
     * ContextRegistry constructor.
     * @param array $contexts
     */
    public function __construct(array $contexts)
    {
        $this->contexts = $contexts;
    }


    public function getContexts()
    {
        return $this->contexts;
    }

}
