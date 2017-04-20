<?php
namespace Concrete\Core\Form\Context\Registry;

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
