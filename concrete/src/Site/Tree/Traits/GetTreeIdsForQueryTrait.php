<?php

namespace Concrete\Core\Site\Tree\Traits;

use Concrete\Core\Entity\Site\Site;

trait GetTreeIdsForQueryTrait
{

    /**
     * A helper method for getting tree IDs for use in a query. Includes "0" in the query out of simplicity
     *
     * @param Site $site
     * @return array
     */
    public function getTreeIdsForQuery(Site $site): string
    {
        $treeIDs = [0];
        foreach($site->getLocales() as $locale) {
            $tree = $locale->getSiteTree();
            if (is_object($tree)) {
                $treeIDs[] = $tree->getSiteTreeID();
            }
        }
        $treeIDs = implode(',', $treeIDs);
        return $treeIDs;
    }
}
