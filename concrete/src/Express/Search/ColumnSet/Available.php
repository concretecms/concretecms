<?php
namespace Concrete\Core\Express\Search\ColumnSet;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Search\Column\Column;

class Available extends ColumnSet
{

    public function __construct(ExpressCategory $category)
    {
        parent::__construct($category);
        $this->addColumn(new Column('e.exEntryDateCreated', t('Date Added'), array('\Concrete\Core\Express\Search\ColumnSet\DefaultSet', 'getDateAdded')));

        $entity = $category->getExpressEntity();
        if ($entity->supportsCustomDisplayOrder()) {
            $this->addColumn(new Column('e.exEntryDisplayOrder', t('Custom Display Order'), array('\Concrete\Core\Express\Search\ColumnSet\DefaultSet', 'getDisplayOrder')));
        }
    }

}
