<?php

namespace Concrete\Core\Tree\Menu\Item\Category;

use Concrete\Core\Tree\Menu\Item\AbstractItem;
use Concrete\Core\Tree\Node\Type\Category;
use HtmlObject\Element;
use HtmlObject\Link;

abstract class CategoryItem extends AbstractItem
{

    /**
     * @var $category Category
     */
    protected $category;

    /**
     * CategoryItem constructor.
     * @param Category $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }


}