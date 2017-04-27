<?php
namespace Concrete\Core\Permission\Response;

use Permissions;

class SiteResponse extends Response
{

    public function canViewSiteInSelector()
    {
        $home = $this->getPermissionObject()->getSiteHomePageObject();
        $cp = new \Permissions($home);
        return $cp->canViewPageInSitemap();
    }


}
