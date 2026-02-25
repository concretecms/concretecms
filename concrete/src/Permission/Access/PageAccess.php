<?php
namespace Concrete\Core\Permission\Access;

use Concrete\Core\Permission\Access\SiteAccessInterface;

class PageAccess extends Access implements SiteAccessInterface
{

    public function getSite()
    {
        $cID = $this->getPermissionObject()->getCollectionID();
        $page = \Page::getByID($cID);
        return $page->getSite();
    }

    public static function usePermissionCollectionIDForIdentifier()
    {
        static $usePermissionsCollectionID;
        if (!isset($usePermissionsCollectionID)) {
            // we do this because these five items are known to be OK for caching permissions against
            // page IDs (well file_uploader is unrelated but we still need to check for it or it will trip this check.)
            // if these are the only things included in your site then you should be able to use permissionsCollectionID for
            // checking permissions against, which will dramatically improve performance.
            // If you have any custom access entities though you won't be able to use this.
            // Obviously a better way of doing this would be to retrieve all access entity types, run through each and see whether
            // they support it but this is better for performance.
            try {
                $db = \Database::get();
                $q = "select pal.paID from PagePermissionAssignments ppa inner join PermissionAccessList pal on (ppa.paID = pal.paID) inner join PermissionAccessEntities pae on pal.peID = pae.peID inner join PermissionAccessEntityTypes paet on pae.petID = paet.petID  where paet.petHandle not in ('group', 'user', 'group_set', 'group_combination', 'file_uploader')";
                $paID = $db->GetOne($q);
                if ($paID) {
                    $usePermissionsCollectionID = false;
                } else {
                    $usePermissionsCollectionID = true;
                }
            } catch (\Exception $e) {
                $usePermissionsCollectionID = false;
            }
        }

        return $usePermissionsCollectionID;
    }
}
