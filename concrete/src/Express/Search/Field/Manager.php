<?php
namespace Concrete\Core\Express\Search\Field;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Entity\Express\OneToManyAssociation;
use Concrete\Core\Search\Field\AttributeKeyField;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Field\Manager as FieldManager;

class Manager extends FieldManager
{
    /**
     * @var ExpressCategory
     */
    protected $expressCategory;

    protected $loaded = false;

    public function setExpressCategory(ExpressCategory $expressCategory)
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

        $associations = $this->expressCategory->getExpressEntity()->getAssociations();
        $group = [];
        if (count($associations)) {
            foreach($associations as $association) {
                if ($association instanceof ManyToManyAssociation || $association instanceof ManyToOneAssociation) {
                    $group[] = new AssociationField($association);
                }
            }
        }

        if (count($group)) {
            $this->addGroup(t('Associations'), $group);
        }

        $setManager = $this->expressCategory->getSetManager();
        $this->populateAttributeGroups($setManager);
    }

}
