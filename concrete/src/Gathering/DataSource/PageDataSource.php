<?php
namespace Concrete\Core\Gathering\DataSource;

use Concrete\Core\Gathering\DataSource\Configuration\Configuration as GatheringDataSourceConfiguration;

class PageDataSource extends DataSource
{
    public function createConfigurationObject(Gathering $ga, $post)
    {
        $o = new PageGatheringDataSourceConfiguration();
        if ($post['ptID']) {
            $o->setPageTypeID($post['ptID']);
        }

        return $o;
    }

    public function createGatheringItems(GatheringDataSourceConfiguration $configuration)
    {
        $pl = new PageList();
        $pl->ignoreAliases();
        $pl->ignorePermissions();
        $gathering = $configuration->getGatheringObject();
        if ($gathering->getGatheringDateLastUpdated()) {
            $pl->filterByPublicDate($gathering->getGatheringDateLastUpdated(), '>');
        }
        $ptID = $configuration->getPageTypeID();
        if ($ptID > 0) {
            $pl->filterByPageTypeID($ptID);
        }
        $pages = $pl->get();
        $items = array();
        foreach ($pages as $c) {
            $item = PageGatheringItem::add($configuration, $c);
            if (is_object($item)) {
                $items[] = $item;
            }
        }

        return $items;
    }
}
