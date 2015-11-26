<?php
namespace Concrete\Core\User\Search\ColumnSet;

class Available extends DefaultSet
{
    protected $attributeClass = 'UserAttributeKey';

    public function __construct()
    {
        parent::__construct();
    }
}
