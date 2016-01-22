<?php
namespace Concrete\Core\Page\Stack;

use Concrete\Core\Page\Page;

class Formatter
{

    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function getIconElement()
    {
        switch($this->page->getCollectionTypeHandle()) {
            case STACK_CATEGORY_PAGE_TYPE:
                return '<i class="fa fa-folder"></i>';
            case STACKS_PAGE_TYPE:
                return '<i class="fa fa-bars"></i>';

        }
    }

}
