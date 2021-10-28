<?php
namespace Concrete\Core\File\Component\Chooser\Option;

use Concrete\Core\File\Component\Chooser\ChooserOptionInterface;
use Concrete\Core\File\Component\Chooser\OptionSerializableTrait;

class SavedSearchOption implements ChooserOptionInterface
{

    use OptionSerializableTrait;

    public function getComponentKey(): string
    {
        return 'saved-search';
    }

    public function getTitle(): string
    {
        return t('Saved Searches');
    }

}