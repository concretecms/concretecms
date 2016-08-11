<?php
namespace Concrete\Core\User\Group;

use Concrete\Core\Search\Column\Column;
use URL;

class GroupSearchColumnSet extends \Concrete\Core\Search\Column\Set
{
    public static function getGroupName($g)
    {
        return '<a data-group-name="' . $g->getGroupDisplayName(false) . '" href="' . URL::to('/dashboard/users/groups', 'edit', $g->getGroupID()) . '" data-group-id="' . $g->getGroupID() . '" href="#">' . $g->getGroupDisplayName() . '</a>';
    }

    public function __construct()
    {
        $this->addColumn(new Column('gName', t('Name'), array('\Concrete\Core\User\Group\GroupSearchColumnSet', 'getGroupName')));
        $gName = $this->getColumnByKey('gName');
        $this->setDefaultSortColumn($gName, 'asc');
    }
}
