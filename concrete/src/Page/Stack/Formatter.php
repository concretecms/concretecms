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
            default:
                return '<i class="fa fa-cubes"></i>';

        }
    }

    public function getSearchResultsClass()
    {
        switch($this->page->getCollectionTypeHandle()) {
            case STACK_CATEGORY_PAGE_TYPE:
                return 'ccm-search-results-folder ccm-search-results-stackfolder ccm-droppable-search-item';
            default:
                return 'ccm-search-results-stack ccm-undroppable-search-item';
        }
    }

}
