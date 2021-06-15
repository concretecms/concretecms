<?php
namespace Concrete\Core\Tree\Node\Type\Formatter;

class GroupFormatter implements ListFormatterInterface
{
    public function getIconElement()
    {
        return '<i class="fa fa-users"></i>';
    }

    public function getSearchResultsClass()
    {
        return 'ccm-search-results-group';
    }

}