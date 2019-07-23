<?php

namespace Concrete\Core\File\Search\Field;

use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\File\Search\Field\Field\ExtensionField;
use Concrete\Core\File\Search\Field\Field\TypeField;
use Concrete\Core\Search\Field\Manager as FieldManager;

class FileFolderManager extends FieldManager
{
    protected $fileCategory;

    public function __construct(FileCategory $fileCategory)
    {
        $this->fileCategory = $fileCategory;
        $this->addGroup('', [
            new TypeField(),
            new ExtensionField(),
        ]);
    }
}
