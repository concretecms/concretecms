<?php
namespace Concrete\Core\Permission\Registry\Multisite\Access;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Permission\Registry\AbstractAccessRegistry;
use Concrete\Core\Permission\Registry\Entry\Access\Entity\GroupEntity;
use Concrete\Core\Permission\Registry\Entry\Access\PermissionsEntry;
use Concrete\Core\Site\User\Group\Service;

class SiteFileFolderAccessRegistry extends AbstractAccessRegistry
{

    public function __construct(Site $site, Service $groupService)
    {
        $groups = $groupService->getInstanceGroupsBySite($site);
        foreach($groups as $group) {
            $this->addEntry(new PermissionsEntry(new GroupEntity($group), [
                'add_file',
                'search_file_folder'
            ]));
        }
        $this->removeEntry(new PermissionsEntry(new GroupEntity(Service::PARENT_GROUP_PATH), [
            'search_file_folder'
        ]));
    }


}
