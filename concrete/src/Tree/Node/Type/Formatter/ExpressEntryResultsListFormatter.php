<?php
namespace Concrete\Core\Tree\Node\Type\Formatter;

/**
 * @since 8.0.0
 */
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