<?php

declare(strict_types=1);

namespace Concrete\Core\User\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;

defined('C5_EXECUTE') or die('Access Denied.');

class DirectMembershipsColumn extends Column
{
    public function getColumnKey()
    {
        return 'directMemberships';
    }

    public function getColumnName()
    {
        return t('Direct Memberships');
    }

    public function getColumnCallback()
    {
        return [MembershipsColumnFormatter::class, 'getDirectMemberships'];
    }

    public function isColumnSortable()
    {
        return false;
    }
}
