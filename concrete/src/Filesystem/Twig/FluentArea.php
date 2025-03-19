<?php

namespace Concrete\Core\Filesystem\Twig;

use Concrete\Core\Area\Area;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;

class FluentArea
{
    /**
     * @var Area|Stack
     */
    private $parent;
    /**
     * @var Page|null
     */
    private $page;

    /**
     * @param Area|Stack $parent
     */
    public function __construct($parent, ?Page $page = null)
    {
        if (!$parent instanceof Area && !$parent instanceof Stack) {
            throw new \InvalidArgumentException('Parent must be an Area or a Stack');
        }

        $this->parent = $parent;
        $this->page = $page;
    }

    public function display(): string
    {
        ob_start();
        $this->parent->display($this->page);
        return ob_get_clean();
    }

    public function __toString(): string
    {
        return $this->display();
    }

    public function __call($name, $arguments)
    {
        $result = $this->parent->$name(...$arguments);
        if ($result !== null) {
            return $result;
        }

        return $this;
    }
}