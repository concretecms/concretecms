<?php

declare(strict_types=1);

namespace Concrete\Core\User\Search\ColumnSet;

use Concrete\Core\User\Search\ColumnSet\Column\DateLastLoginColumn;
use Concrete\Core\User\Search\ColumnSet\Column\DirectMembershipsColumn;
use Concrete\Core\User\Search\ColumnSet\Column\DirectMembershipsCountColumn;
use Concrete\Core\User\Search\ColumnSet\Column\IndirectMembershipsColumn;
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
        $this->addColumn(new DirectMembershipsCountColumn());
        $this->addColumn(new DirectMembershipsColumn());
        $this->addColumn(new IndirectMembershipsColumn());
    }
}
