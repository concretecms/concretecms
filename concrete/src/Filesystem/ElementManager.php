<?php

namespace Concrete\Core\Filesystem;

class ElementManager
{
    /**
     * @var \Concrete\Core\Filesystem\Element[]
     */
    protected $registry = [];

    /**
     * @param string $element the element name
     *
     * The additional optional arguments (read with func_get_args(), see Element::populateFromArguments()) can be, in any order:
     * - \Concrete\Core\Page\Page: the page where the element will be rendered
     * - array: the arguments to be used when calling the constructor of the element controller
     * - string: the handle of the package defining this element
     *
     * @return \Concrete\Core\Filesystem\Element
     */
    public function get($element)
    {
        if (isset($this->registry[$element])) {
            $o = $this->registry[$element];
            if ($o instanceof \Closure) {
                $element = $o();
            } else {
                $element = $o;
            }
            $element->populateFromArguments(func_get_args());

            return $element;
        } else {
            $class = new \ReflectionClass(Element::class);

            return $class->newInstanceArgs(func_get_args());
        }
    }

    /**
     * Register an element.
     *
     * @param string $element the element name
     * @param \Concrete\Core\Filesystem\Element $object the element instance
     */
    public function register($element, $object)
    {
        $this->registry[$element] = $object;
    }

    /**
     * Unregister an element.
     *
     * @param string $element the element name
     */
    public function unregister($element)
    {
        unset($this->registry[$element]);
    }
}
