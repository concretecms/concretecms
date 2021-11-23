<?php
namespace Concrete\Core\Tree\Node\Type\Formatter;

class SavedSearchListFormatter implements ListFormatterInterface
{
    public function getIconElement()
    {
        return '<i class="fas fa-search-plus"></i>';
    }

    public function getSearchResultsClass()
    {
        return 'ccm-search-results-folder';
    }

}