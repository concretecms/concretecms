<?php
namespace Concrete\Core\Express\Search\ColumnSet;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Search\Column\ExpressAttributeKeyColumn;
use PermissionKey;
use Concrete\Core\User\User;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Search\Column\AttributeKeyColumn;

class ColumnSet extends Set
{

    protected $category;

    public function __construct(ExpressCategory $category)
    {
        $this->category = $category;
    }

    public function __wakeup()
    {
        $this->category = \Core::make('Concrete\Core\Attribute\Category\ExpressCategory');
        parent::__wakeup();
    }

    public function getAttributeKeyColumn($akHandle)
    {
        $ak = $this->category->getAttributeKeyByHandle($akHandle);
        if (is_object($ak)) {
            $col = new ExpressAttributeKeyColumn($ak);
            return $col;
        }
    }

}
