<?php
namespace Concrete\Core\Tree\Node\Type\Formatter;

class LegacyGroupFormatter implements ListFormatterInterface
{
    public function getIconElement()
    {
        return '<i class="fa fa-users-folder"></i>';
    }

    public function getSearchResultsClass()
    {
        return 'ccm-search-results-group';
    }

}