<?php
namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\Search\Field\FieldInterface;

class ExtensionField implements FieldInterface
{

    public function getKey()
    {
        return 'extension';
    }

    public function getDisplayName()
    {
        return t('Extension');
    }



}
