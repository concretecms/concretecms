<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver\Traits;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Data\Field\DataField;
use Concrete\Core\Summary\Data\Field\DataFieldInterface;
use Concrete\Core\Summary\Data\Field\FieldInterface;
use Concrete\Core\Summary\Data\Field\ImageDataFieldData;
use Concrete\Core\Summary\Data\Field\TopicsDataFieldData;

trait GetCategoriesTrait 
{

    abstract public function getCategoriesAttributeKeyHandle();
    
    public function getCategoriesDataField(ObjectInterface $mixed) : ?DataFieldInterface
    {
        $categories = $mixed->getAttribute($this->getCategoriesAttributeKeyHandle());
        if ($categories) {
            return new DataField(
                FieldInterface::FIELD_CATEGORIES,
                new TopicsDataFieldData($categories)
            );
        }
        return null;
    }
    
}
