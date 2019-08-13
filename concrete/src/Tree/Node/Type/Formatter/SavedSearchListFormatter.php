<?php
namespace Concrete\Core\Tree\Node\Type\Formatter;

/**
 * @since 8.0.0
 */
class SavedSearchListFormatter implements ListFormatterInterface
{
    public function getIconElement()
    {
        return '<i class="fa fa-search-plus"></i>';
    }

    public function getSearchResultsClass()
    {
        return 'ccm-search-results-folder';
    }

}