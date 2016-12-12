<?php
namespace Concrete\Core\Tree\Node\Type\Formatter;

class ExpressEntryResultsListFormatter implements ListFormatterInterface
{
    public function getIconElement()
    {
        return '<i class="fa fa-th-list"></i>';
    }

    public function getSearchResultsClass()
    {
        return 'ccm-search-results-stack';
    }

}