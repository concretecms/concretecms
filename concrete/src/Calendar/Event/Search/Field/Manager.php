<?php

namespace Concrete\Core\Calendar\Event\Search\Field;

use Concrete\Core\Attribute\Category\EventCategory;
use Concrete\Core\Search\Field\AttributeKeyField;
use Concrete\Core\Search\Field\Manager as FieldManager;

class Manager extends FieldManager
{

    /**
     * @var \Concrete\Core\Attribute\Category\EventCategory
     */
    protected $eventCategory;

    public function __construct(EventCategory $eventCategory)
    {
        $this->eventCategory = $eventCategory;
        $attributes = [];
        foreach ($eventCategory->getSearchableList() as $key) {
            $field = new AttributeKeyField($key);
            $attributes[] = $field;
        }
        $this->addGroup(t('Custom Attributes'), $attributes);
    }
}
