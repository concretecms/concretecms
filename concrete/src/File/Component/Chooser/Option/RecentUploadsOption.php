<?php
namespace Concrete\Core\File\Component\Chooser\Option;

use Concrete\Core\File\Component\Chooser\ChooserOptionInterface;
use Concrete\Core\File\Component\Chooser\OptionSerializableTrait;

class RecentUploadsOption implements ChooserOptionInterface
{

    use OptionSerializableTrait;

    public function getComponentKey(): string
    {
        return 'recent-uploads';
    }

    public function getTitle(): string
    {
        return t('Recent Uploads');
    }

}