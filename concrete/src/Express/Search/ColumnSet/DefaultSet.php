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

    public function __construct(ExpressCategory $category)
    {
        parent::__construct($category);
        $date = $this->getColumnByKey('e.exEntryDateCreated');
        $this->setDefaultSortColumn($date, 'desc');

        $i = 0;
        foreach($category->getList() as $ak) {
            $this->addColumn(new AttributeKeyColumn($ak));
            $i++;
            if ($i == 2) {
                break;
            }
        }
    }



}
