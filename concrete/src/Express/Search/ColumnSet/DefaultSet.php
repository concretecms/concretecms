<?php
namespace Concrete\Core\Express\Search\ColumnSet;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\Search\ColumnSet\Column\DateCreatedColumn;
use Concrete\Core\Express\Search\ColumnSet\Column\DateLastModifiedColumn;
use Concrete\Core\Express\Search\ColumnSet\Column\DisplayOrderColumn;
use Concrete\Core\Search\Column\AttributeKeyColumn;
use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\ExpressAttributeKeyColumn;
use Core;

class DefaultSet extends ColumnSet
{

    /**
     * @param Entry $entry
     * @return string
     */
    public static function getDateAdded(Entry $entry)
    {
        return Core::make('helper/date')->formatDateTime($entry->getDateCreated());
    }

    /**
     * Return the Date this entry was last modified or date created
     * if getDateModified returns null (for backwards compaitibility)
     *
     * @param Entry $entry
     * @return string
     */
    public static function getDateModified(Entry $entry)
    {
        $dateModified = $entry->getDateModified();
        if (!empty($dateModified)) {
            return Core::make('helper/date')->formatDateTime($dateModified);
        } else {
            return Core::make('helper/date')->formatDateTime($entry->getDateCreated());
        }

    }

    /**
     * @param Entry $entry
     * @return integer
     */
    public static function getDisplayOrder(Entry $entry)
    {
        return $entry->getEntryDisplayOrder();
    }

    /**
     * DefaultSet constructor.
     * @param ExpressCategory $category
     */
    public function __construct(ExpressCategory $category)
    {
        parent::__construct($category);

        $this->addColumn(new DateCreatedColumn());
        $this->addColumn(new DateLastModifiedColumn());

        $entity = $category->getExpressEntity();
        if ($entity->supportsCustomDisplayOrder()) {
            $this->addColumn(new DisplayOrderColumn());
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
            $this->addColumn(new ExpressAttributeKeyColumn($ak));
            $i++;
            if ($i == 2) {
                break;
            }
        }
    }



}
