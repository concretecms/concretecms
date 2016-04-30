<?php
namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\Search\Field\FieldInterface;

class TypeField implements FieldInterface
{

    public function getKey()
    {
        return 'type';
    }

    public function getDisplayName()
    {
        return t('Type');
    }


}
