<?php
namespace Concrete\Core\Page\Desktop;

use Concrete\Core\Page\PageList;

class DesktopList
{
    public static function getMyDesktop()
    {
        $list = new PageList();
        $list->includeSystemPages();
        $list->filterByAttribute('is_desktop', true);
        $list->sortByDesktopPriority('desc');
        $results = $list->getResults();
        if (!empty($results)) {
            return $results[0];
        }
    }
}
