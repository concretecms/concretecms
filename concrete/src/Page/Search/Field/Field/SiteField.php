<?php
namespace Concrete\Core\Page\Search\Field\Field;

use Concrete\Core\Form\Service\Widget\SiteSelector;
use Concrete\Core\Page\PageList;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\Field\AbstractSiteField;
use Concrete\Core\Search\ItemList\ItemList;

class SiteField extends AbstractSiteField
{

    /**
     * @param PageList $list
     */
    public function filterList(ItemList $list)
    {
        if (!isset($this->data['siteID']) || $this->data['siteID'] === 'current') {
            $site = \Core::make('site')->getActiveSiteForEditing();
        } else if ($this->data['siteID'] === 'all') {
            $list->setSiteTreeToAll();
        } else {
            $site = \Core::make('site')->getByID($this->data['siteID']);
        }

        if ($site) {
            $sp = new \Permissions($site);
            if ($sp->canViewSiteInSelector()) {
                $list->setSiteTreeObject($site->getSiteTreeObject());
            }
        }
    }


}
