<?php
namespace Concrete\Core\Express\Search\Field;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Field\Manager as FieldManager;
use Concrete\Core\Site\InstallationService;

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
        $properties = [new KeywordsField()];
        $installationService = app(InstallationService::class);
        if ($installationService->isMultisiteEnabled()) {
            $properties[] = new SiteField();
        }

        $this->addGroup(t('Core Properties'), $properties);
        $associations = $this->expressCategory->getExpressEntity()->getAssociations();
        $group = [];
        if (count($associations)) {
            foreach($associations as $association) {
                $group[] = new AssociationField($association);
            }
        }

        if (count($group)) {
            $this->addGroup(t('Associations'), $group);
        }

        $setManager = $this->expressCategory->getSetManager();
        $this->populateAttributeGroups($setManager);
    }

}
