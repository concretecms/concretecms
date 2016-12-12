<?php
namespace Concrete\Core\Express\Search\ColumnSet;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Search\Column\AttributeKeyColumn;
use Core;

class DefaultSet extends Available
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
        $entity = $category->getExpressEntity();
        if ($entity->supportsCustomDisplayOrder()) {
            $column = $this->getColumnByKey('e.exEntryDisplayOrder');
            $this->setDefaultSortColumn($column, 'asc');
        } else {
            $column = $this->getColumnByKey('e.exEntryDateCreated');
            $this->setDefaultSortColumn($column, 'desc');
        }
        $this->removeColumnByKey('e.exEntryDisplayOrder'); // It shouldn't be in the set
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
