<?php
namespace Concrete\Core\File\Component\Chooser\Option;

use Concrete\Core\File\Component\Chooser\ChooserOptionInterface;
use Concrete\Core\File\Component\Chooser\OptionSerializableTrait;

class SearchOption implements ChooserOptionInterface
{

    use OptionSerializableTrait;

    public function getComponentKey(): string
    {
        return 'search';
    }

    public function getTitle(): string
    {
        return t('Search');
    }

}