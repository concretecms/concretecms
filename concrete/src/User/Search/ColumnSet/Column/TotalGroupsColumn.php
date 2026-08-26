<?php

declare(strict_types=1);

namespace Concrete\Core\User\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;

defined('C5_EXECUTE') or die('Access Denied.');

class TotalGroupsColumn extends Column
{
    public function getColumnKey()
    {
        return 'totalGroups';
    }

    public function getColumnName()
    {
        return t('Total Groups');
    }

    public function getColumnCallback()
    {
        return [MembershipsColumnFormatter::class, 'getTotalGroupsCount'];
    }

    public function isColumnSortable()
    {
        return false;
    }
}