<?php
namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\Search\Field\FieldInterface;

class DateAddedField implements FieldInterface
{

    public function getKey()
    {
        return 'date_added';
    }

    public function getDisplayName()
    {
        return t('Date Added');
    }



}
