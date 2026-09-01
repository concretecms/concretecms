<?php

declare(strict_types=1);

namespace Concrete\Core\User\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;

defined('C5_EXECUTE') or die('Access Denied.');

class IndirectMembershipsColumn extends Column
{
    public function getColumnKey()
    {
        return 'indirectMemberships';
    }

    public function getColumnName()
    {
        return t('Indirect Memberships');
    }

    public function getColumnCallback()
    {
        return [MembershipsColumnFormatter::class, 'getIndirectMemberships'];
    }

    public function isColumnSortable()
    {
        return false;
    }
}
