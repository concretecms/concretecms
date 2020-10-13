<?php
namespace Concrete\Core\File\Component\Chooser\Option;

use Concrete\Core\File\Component\Chooser\OptionSerializableTrait;
use Concrete\Core\File\Component\Chooser\UploaderOptionInterface;

class FileUploadOption implements UploaderOptionInterface
{

    use OptionSerializableTrait;

    public function getComponentKey(): string
    {
        return 'file-upload';
    }

    public function getTitle(): string
    {
        return t('File Upload');
    }

}