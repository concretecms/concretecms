<?php

declare(strict_types=1);

namespace Concrete\Core\User\Search\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ColumnExportableInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class UserGroupListColumn extends Column implements ColumnExportableInterface
{
    public function getColumnKey()
    {
        return 'userGroupList';
    }

    public function getColumnName()
    {
        return t('User Group List');
    }

    public function getColumnCallback()
    {
        return [MembershipsColumnFormatter::class, 'getUserGroupList'];
    }

    public function isColumnSortable()
    {
        return false;
    }

    public function getColumnExportValue($userInfo)
    {
        return MembershipsColumnFormatter::getUserGroupListExport($userInfo);
    }
}