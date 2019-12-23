<?php
namespace Concrete\Core\Summary\Data\Extractor\Driver\Traits;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\ObjectInterface;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Data\Field\DataField;
use Concrete\Core\Summary\Data\Field\DataFieldInterface;
use Concrete\Core\Summary\Data\Field\FieldInterface;
use Concrete\Core\Summary\Data\Field\ImageDataFieldData;

trait GetThumbnailTrait 
{

    abstract public function getThumbnailAttributeKeyHandle();
    
    public function getThumbnailDataField(ObjectInterface $mixed) : ?DataFieldInterface
    {
        $thumbnail = $mixed->getAttribute($this->getThumbnailAttributeKeyHandle());
        if ($thumbnail) {
            return new DataField(
                FieldInterface::FIELD_THUMBNAIL,
                new ImageDataFieldData($thumbnail)
            );
        }
        return null;
    }
    
}
