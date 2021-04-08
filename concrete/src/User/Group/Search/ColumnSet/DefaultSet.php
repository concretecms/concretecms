<?php

namespace Concrete\Core\User\Group\Search\ColumnSet;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\User\Group\Search\ColumnSet\Column\NameColumn;
use Concrete\Core\User\Group\Search\ColumnSet\Column\TypeColumn;

class DefaultSet extends ColumnSet
{
    protected $attributeClass = 'CollectionAttributeKey';

    public function __construct()
    {
        $this->addColumn(new NameColumn());
        $this->addColumn(new Column('type', t('Type'), ['Concrete\Core\User\Group\Search\ColumnSet\Available', 'getType'], false));
        $this->addColumn(new Column('members', t('Members'), ['Concrete\Core\User\Group\Search\ColumnSet\Available', 'getMemberCount'], false));
        $groupId = $this->getColumnByKey('name');
        $this->setDefaultSortColumn($groupId, 'asc');
    }
}
