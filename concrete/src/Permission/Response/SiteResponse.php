<?php
namespace Concrete\Core\Permission\Response;

use Concrete\Core\Permission\Checker as Permissions;

class SiteResponse extends Response
{

    public function canViewSiteInSelector()
    {
        $home = $this->getPermissionObject()->getSiteHomePageObject();
        $cp = new \Concrete\Core\Permission\Checker($home);
        return $cp->canViewPageInSitemap();
    }


}
