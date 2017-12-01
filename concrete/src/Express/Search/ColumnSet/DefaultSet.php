<?php
namespace Concrete\Core\Express\Search\ColumnSet;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Search\Column\AttributeKeyColumn;
use Concrete\Core\Search\Column\Column;
use Core;

class DefaultSet extends ColumnSet
{

    public static function getDateAdded(Entry $entry)
    {
        return Core::make('helper/date')->formatDateTime($entry->getDateCreated());
    }

    public static function getDisplayOrder(Entry $entry)
    {
        return $entry->getEntryDisplayOrder();
    }

    public function __construct(ExpressCategory $category)
    {
        parent::__construct($category);

        $this->addColumn(new Column('e.exEntryDateCreated', t('Date Added'), array('\Concrete\Core\Express\Search\ColumnSet\DefaultSet', 'getDateAdded')));

        $entity = $category->getExpressEntity();
        if ($entity->supportsCustomDisplayOrder()) {
            $this->addColumn(new Column('e.exEntryDisplayOrder', t('Custom Display Order'), array('\Concrete\Core\Express\Search\ColumnSet\DefaultSet', 'getDisplayOrder')));
        }

        $entity = $category->getExpressEntity();
        if ($entity->supportsCustomDisplayOrder()) {
            $column = $this->getColumnByKey('e.exEntryDisplayOrder');
            $this->setDefaultSortColumn($column, 'asc');
        } else {
            $column = $this->getColumnByKey('e.exEntryDateCreated');
            $this->setDefaultSortColumn($column, 'desc');
        }

        $i = 0;
        foreach($category->getSearchableList() as $ak) {
            $this->addColumn(new AttributeKeyColumn($ak));
            $i++;
            if ($i == 2) {
                break;
            }
        }
    }



}
