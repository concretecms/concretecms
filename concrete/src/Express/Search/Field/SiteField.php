<?php
namespace Concrete\Core\Express\Search\Field;

use Concrete\Core\Express\EntryList;
use Concrete\Core\Page\PageList;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Field\AbstractSiteField;
use Concrete\Core\Search\ItemList\ItemList;

class SiteField extends AbstractSiteField
{

    /**
     * @param EntryList $list
     */
    public function filterList(ItemList $list)
    {
        if (!isset($this->data['siteID']) || $this->getData('siteID') === 'current') {
            $site = app('site')->getActiveSiteForEditing();
        } else if ($this->getData('siteID') === 'all') {
            //$list->setSiteTreeToAll();
        } else {
            $site = app('site')->getByID($this->getData('siteID'));
        }

        if ($site) {
            $sp = new Checker($site);
            if ($sp->canViewSiteInSelector()) {
                $list->filterBySite($site);
            }
        }
    }


}
