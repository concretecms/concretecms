<?php
namespace Concrete\Core\User\Search\ColumnSet;

use Concrete\Core\User\Search\ColumnSet\Column\DateLastLoginColumn;
use Concrete\Core\User\Search\ColumnSet\Column\UserIDColumn;

class Available extends DefaultSet
{
    protected $attributeClass = 'UserAttributeKey';

    public function __construct()
    {
        parent::__construct();
        $this->addColumn(new UserIDColumn());
        $this->addColumn(new DateLastLoginColumn());
    }
}
