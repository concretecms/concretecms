<?php
namespace Concrete\Core\Express\Search\ColumnSet;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Express\Search\Column\AssociationColumn;
use Concrete\Core\Search\Column\Column;

class Available extends DefaultSet
{

    public function __construct(ExpressCategory $category)
    {
        parent::__construct($category);

        $associations = $category->getExpressEntity()->getAssociations();
        foreach($associations as $association) {
            $column = new AssociationColumn($association);
            $this->addColumn($column);
        }
    }

}
