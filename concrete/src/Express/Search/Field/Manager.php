<?php
namespace Concrete\Core\Express\Search\Field;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Search\Field\AttributeKeyField;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Field\Manager as FieldManager;

class Manager extends FieldManager
{
    protected $expressCategory;
    protected $loaded = false;

    /**
     * @param mixed $expressCategory
     */
    public function setExpressCategory($expressCategory)
    {
        $this->expressCategory = $expressCategory;
    }

    public function getGroups()
    {
        if (!$this->loaded) {
            $this->populateGroups();
            $this->loaded = true;
        }
        return $this->groups;
    }

    public function getFieldByKey($key)
    {
        if (!$this->loaded) {
            $this->populateGroups();
            $this->loaded = true;
        }
        return parent::getFieldByKey($key);
    }

    public function getFieldsFromRequest(array $request)
    {
        if (!$this->loaded) {
            $this->populateGroups();
            $this->loaded = true;
        }
        return parent::getFieldsFromRequest($request);
    }

    protected function populateGroups()
    {
        $this->addGroup(t('Core Properties'), [
            new KeywordsField()
        ]);

        $setManager = $this->expressCategory->getSetManager();
        $this->populateAttributeGroups($setManager);
    }

}
