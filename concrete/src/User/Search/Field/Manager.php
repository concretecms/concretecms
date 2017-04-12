<?php
namespace Concrete\Core\User\Search\Field;

use Concrete\Core\Attribute\Category\UserCategory;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Search\Field\AttributeKeyField;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Field\Manager as FieldManager;
use Concrete\Core\User\Search\Field\Field\DateAddedField;
use Concrete\Core\User\Search\Field\Field\GroupSetField;
use Concrete\Core\User\Search\Field\Field\IsActiveField;
use Concrete\Core\User\Search\Field\Field\UserGroupField;
use Concrete\Core\User\Search\Field\Field\IsValidatedField;

class Manager extends FieldManager
{
    protected $fileCategory;

    public function __construct(UserCategory $fileCategory)
    {
        $this->fileCategory = $fileCategory;
        $this->addGroup(t('Core Properties'), [
            new KeywordsField(),
            new UserGroupField(),
            new IsActiveField(),
            new IsValidatedField(),
            new DateAddedField(),
            new GroupSetField()
        ]);

        $service = \Core::make(CategoryService::class);
        $setManager = $service->getByHandle('user')->getController()->getSetManager();
        $attributeSets = $setManager->getAttributeSets();
        $unassigned = $setManager->getUnassignedAttributeKeys();

        $attributes = [];
        foreach($attributeSets as $set) {
            foreach($set->getAttributeKeys() as $key) {
                $field = new AttributeKeyField($key);
                $attributes[] = $field;
            }
            $this->addGroup($set->getAttributeSetDisplayName(), $attributes);
        }

        $attributes = [];
        foreach($unassigned as $key) {
            $field = new AttributeKeyField($key);
            $attributes[] = $field;
        }
        $this->addGroup(t('Other Attributes'), $attributes);
    }
}
