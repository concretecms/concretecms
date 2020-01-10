<?php
namespace Concrete\Controller\Permissions\Access\Entity;

use Concrete\Core\Permission\Access\Entity\GroupCombinationEntity;
use Concrete\Core\Permission\Access\Entity\GroupSetEntity;

class GroupCombination extends AccessEntity
{

    public function deliverEntity()
    {
        $groups = [];
        $gIDs = (array) $this->request->request->get('gID');
        foreach($gIDs as $gID) {
            $g = \Concrete\Core\User\Group\Group::getByID($gID);
            if ($g) {
                $groups[] = $g;
            }
        }
        if ($groups) {
            return GroupCombinationEntity::getOrCreate($groups);
        }
    }
}
