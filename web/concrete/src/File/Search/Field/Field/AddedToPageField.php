<?php
namespace Concrete\Core\File\Search\Field\Field;

use Concrete\Core\Search\Field\FieldInterface;

class AddedToPageField implements FieldInterface
{

    public function getKey()
    {
        return 'added_to_page';
    }

    public function getDisplayName()
    {
        return t('Added to Page');
    }



}
