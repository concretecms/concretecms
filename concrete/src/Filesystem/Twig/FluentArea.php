<?php

namespace Concrete\Core\Filesystem\Twig;

use Concrete\Core\Area\Area;
use Concrete\Core\Area\ContainerArea;
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
     * @param Area|Stack|ContainerArea $parent
     */
    public function __construct($parent, ?Page $page = null)
    {
        if (
            !$parent instanceof Area
            && !$parent instanceof Stack
            && !$parent instanceof ContainerArea
        ) {
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

    public function ifPopulatedOrEditMode(?Page $c = null): ?FluentArea
    {
        $c = $c ?: $this->page;
        return $this->ifEditMode($c) ?: $this->ifPopulated($c);
    }

    private function ifPopulated(?Page $c = null): ?FluentArea
    {
        $c = $c ?: $this->page;
        return ($c && (int) $this->getTotalBlocksInArea($c) > 0) ? $this : null;
    }

    private function ifEditMode(?Page $c = null): ?FluentArea
    {
        $c = $c ?: $this->page;
        return ($c && $c->isEditMode()) ? $this : null;
    }
}