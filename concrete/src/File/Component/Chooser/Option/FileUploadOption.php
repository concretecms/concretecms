<?php
namespace Concrete\Core\File\Component\Chooser\Option;

use Concrete\Core\File\Component\Chooser\DefaultUploadDirectoryIdTrait;
use Concrete\Core\File\Component\Chooser\OptionSerializableTrait;
use Concrete\Core\File\Component\Chooser\UploaderOptionInterface;

class FileUploadOption implements UploaderOptionInterface
{

    use OptionSerializableTrait;
    use DefaultUploadDirectoryIdTrait;

    public function getComponentKey(): string
    {
        return 'file-upload';
    }

    public function getTitle(): string
    {
        return t('File Upload');
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'componentKey' => $this->getComponentKey(),
            'title' => $this->getTitle(),
            'data' => [
                'uploadDirectoryId' => $this->getDefaultUploadDirectoryId(),
            ],
        ];
    }

}