<?php

declare(strict_types=1);

namespace Concrete\Core\User\Search\ColumnSet;

use Concrete\Core\User\Search\ColumnSet\Column\DateLastLoginColumn;
use Concrete\Core\User\Search\ColumnSet\Column\TotalGroupsColumn;
use Concrete\Core\User\Search\ColumnSet\Column\UserGroupListColumn;
use Concrete\Core\User\Search\ColumnSet\Column\UserIDColumn;

defined('C5_EXECUTE') or die('Access Denied.');

class Available extends DefaultSet
{
    protected $attributeClass = 'UserAttributeKey';

    public function __construct()
    {
        parent::__construct();
        $this->addColumn(new UserIDColumn());
        $this->addColumn(new DateLastLoginColumn());
        $this->addColumn(new TotalGroupsColumn());
        $this->addColumn(new UserGroupListColumn());
    }
}