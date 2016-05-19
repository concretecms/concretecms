<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Entity\Attribute\Key\FileKey;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Type;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class LegacyCategory implements CategoryInterface, StandardSearchIndexerInterface
{

    use StandardCategoryTrait;

    public function __construct(Application $application, EntityManager $entityManager)
    {
        $this->application = $application;
        $this->entityManager = $entityManager;
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    protected function getLegacyKeyClass()
    {
        $class = camelcase($this->getCategoryEntity()->getAttributeKeyCategoryHandle());
        $prefix = ($this->getCategoryEntity()->getPackageID() > 0) ?
            $this->getCategoryEntity()->getPackageHandle() : false;
        $class = core_class('Core\\Attribute\\Key\\' . $class . 'Key', $prefix);
        return $class;
    }

    public function getSearchIndexer()
    {
        /*
        $indexer = $this->application->make('Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexer');

        return $indexer;
        */
        return false;
    }

    public function getIndexedSearchTable()
    {
        $class = $this->getLegacyKeyClass();
        if (method_exists($class, 'getIndexedSearchTable')) {
            return $class::getIndexedSearchTable();
        }
    }

    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return false;
    }

    public function getSearchIndexFieldDefinition()
    {
        $class = $this->getLegacyKeyClass();
        return $class::getSearchIndexFieldDefinition();
    }

    public function getList()
    {
        $class = $this->getLegacyKeyClass();
        return $class::getList();
    }

    public function getAttributeValues($mixed)
    {
        $arguments = func_get_args();
        return call_user_func_array(array($this->getLegacyKeyClass(), 'getAttributes'), $arguments);
    }

    public function addFromRequest(\Concrete\Core\Entity\Attribute\Type $type, Request $request)
    {
        // TODO: Implement addFromRequest() method.
    }

    public function updateFromRequest(Key $key, Request $request)
    {
        // TODO: Implement updateFromRequest() method.
    }

    public function getAttributeKeyByID($akID)
    {
        // TODO: Implement getAttributeKeyByID() method.
    }

    public function deleteKey(Key $key)
    {
        // TODO: Implement deleteKey() method.
    }

    public function deleteValue(AttributeValueInterface $value)
    {
        // TODO: Implement deleteValue() method.
    }

    public function getAttributeValue(Key $key, $mixed)
    {
        // TODO: Implement getAttributeValue() method.
    }



}
