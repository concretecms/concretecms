<?php

declare(strict_types=1);

namespace Concrete\Core\User\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;

defined('C5_EXECUTE') or die('Access Denied.');

class DirectMembershipsCountColumn extends Column
{
    public function getColumnKey()
    {
        return 'directMembershipsCount';
    }

    public function getColumnName()
    {
        return t('# Direct Memberships');
    }

    public function getColumnCallback()
    {
        return [MembershipsColumnFormatter::class, 'getDirectMembershipsCount'];
    }

    public function isColumnSortable()
    {
        return false;
    }
}
