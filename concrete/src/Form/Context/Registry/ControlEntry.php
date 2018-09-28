<?php
namespace Concrete\Core\Form\Context\Registry;

use Concrete\Core\Form\Context\ContextInterface;

class ControlEntry
{

    protected $context;
    protected $handle;
    protected $viewClass;

    /**
     * Entry constructor.
     * @param $context
     * @param $handle
     * @param $viewClass
     */
    public function __construct(ContextInterface $context, $handle, $viewClass)
    {
        $this->context = $context;
        $this->handle = $handle;
        $this->viewClass = $viewClass;
    }

    /**
     * @return ContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @return mixed
     */
    public function getViewClass()
    {
        return $this->viewClass;
    }


}
