<?php
namespace Concrete\Core\File\Component\Chooser\Option;

use Concrete\Core\File\Component\Chooser\ChooserOptionInterface;
use Concrete\Core\File\Component\Chooser\OptionSerializableTrait;

class FileSetsOption implements ChooserOptionInterface
{

    use OptionSerializableTrait;

    public function getComponentKey(): string
    {
        return 'file-sets';
    }

    public function getTitle(): string
    {
        return t('File Sets');
    }

}