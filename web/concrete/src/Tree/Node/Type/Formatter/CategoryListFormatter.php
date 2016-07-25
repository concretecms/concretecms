<?php
namespace Concrete\Core\Tree\Node\Type\Formatter;

class CategoryListFormatter implements ListFormatterInterface
{
    public function getIconElement()
    {
        return '<i class="fa fa-folder"></i>';
    }

    public function getSearchResultsClass()
    {
        return 'ccm-search-results-folder';
    }

}