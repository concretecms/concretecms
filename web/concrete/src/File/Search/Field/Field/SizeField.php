<?php
namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\Search\Field\FieldInterface;

class SizeField implements FieldInterface
{
    public function getKey()
    {
        return 'size';
    }

    public function getDisplayName()
    {
        return t('Size');
    }

}
