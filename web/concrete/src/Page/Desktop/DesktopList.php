<?php
namespace Concrete\Core\Page\Desktop;

use Concrete\Core\Page\PageList;

class DesktopList
{
    public static function getMyDesktop()
    {
        $list = new PageList();
        $list->filterByPageTypeHandle(DESKTOP_PAGE_TYPE);
        $list->includeSystemPages();
        $list->sortByDesktopPriority('desc');
        $results = $list->getResults();
        if (is_object($results[0])) {
            return $results[0];
        }
    }
}
