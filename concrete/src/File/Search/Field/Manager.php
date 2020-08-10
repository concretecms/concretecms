<?php

namespace Concrete\Core\File\Search\Field;

use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\File\Search\Field\Field\AddedToPageField;
use Concrete\Core\File\Search\Field\Field\DateAddedField;
use Concrete\Core\File\Search\Field\Field\ExtensionField;
use Concrete\Core\File\Search\Field\Field\FileSetField;
use Concrete\Core\File\Search\Field\Field\FolderField;
use Concrete\Core\File\Search\Field\Field\SizeField;
use Concrete\Core\File\Search\Field\Field\StorageLocationField;
use Concrete\Core\File\Search\Field\Field\TypeField;
use Concrete\Core\Search\Field\AttributeKeyField;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Field\Manager as FieldManager;

class Manager extends FieldManager
{

    /**
     * @var \Concrete\Core\Attribute\Category\FileCategory
     */
    protected $fileCategory;

    public function __construct(FileCategory $fileCategory)
    {
        $this->fileCategory = $fileCategory;
        $this->addGroup(t('Core Properties'), [
            new KeywordsField(),
            new FolderField(),
            new FileSetField(),
            new ExtensionField(),
            new TypeField(),
            new SizeField(),
            new DateAddedField(),
            new AddedToPageField(),
            new StorageLocationField()
        ]);
        $attributes = [];
        foreach ($fileCategory->getSearchableList() as $key) {
            $field = new AttributeKeyField($key);
            $attributes[] = $field;
        }
        $this->addGroup(t('Custom Attributes'), $attributes);
    }
}
