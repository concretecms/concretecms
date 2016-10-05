<?php
namespace Concrete\Core\Page\Desktop;

use Concrete\Core\Page\PageList;

class DesktopList
{
    public static function getMyDesktop()
    {
        $list = new PageList();
        $list->includeRootPages();
        $list->filterByAttribute('is_desktop', true);
        $list->sortByDesktopPriority('desc');
        $results = $list->getResults();
        if (is_object($results[0])) {
            return $results[0];
        }
    }
}
