<?php
namespace Concrete\Core\Permission\Response;

use Permissions;

/**
 * @since 8.2.0
 */
class SiteResponse extends Response
{

    public function canViewSiteInSelector()
    {
        $home = $this->getPermissionObject()->getSiteHomePageObject();
        $cp = new \Permissions($home);
        return $cp->canViewPageInSitemap();
    }


}
