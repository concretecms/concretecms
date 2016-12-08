<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Attribute\Key\ImportLoader\StandardImportLoader;
use Concrete\Core\Attribute\Key\RequestLoader\StandardRequestLoader;
use Concrete\Core\Attribute\Set;
use Concrete\Core\Entity\Attribute\Key\FileKey;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\LegacyKey;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Entity\Attribute\Value\LegacyValue;
use Concrete\Core\Entity\Package;
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

    public function getLegacyKeyClass()
    {
        $class = camelcase($this->getCategoryEntity()->getAttributeKeyCategoryHandle());
        $prefix = ($this->getCategoryEntity()->getPackageID() > 0) ?
            $this->getCategoryEntity()->getPackageHandle() : false;
        $class = core_class('Core\\Attribute\\Key\\' . $class . 'Key', $prefix);
        return $class;
    }

    public function getSearchIndexer()
    {
        $table = $this->getIndexedSearchTable();
        if ($table) {
            $indexer = $this->application->make('Concrete\Core\Attribute\Category\SearchIndexer\LegacySearchIndexer');
            return $indexer;
        }
    }

    public function getIndexedSearchTable()
    {
        $class = $this->getLegacyKeyClass();
        $o = new $class();
        return $o->getIndexedSearchTable();
    }

    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return false;
    }

    public function getSearchIndexFieldDefinition()
    {
        $class = $this->getLegacyKeyClass();
        $o = new $class();
        return $o->getSearchIndexFieldDefinition();
    }

    public function getAttributeKeyByHandle($akHandle)
    {
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\Key\LegacyKey');
        return $r->findOneBy(['akHandle' => $akHandle]);
    }

    public function getList()
    {
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\Key\LegacyKey');
        $attributes = $r->findBy(array(
            'category' => $this->getCategoryEntity(),
            'akIsInternal' => false,
        ));
        $return = array();
        $class = $this->getLegacyKeyClass();
        foreach($attributes as $ak) {
            $attribute = new $class();
            $attribute->load($ak->getAttributeKeyID());
            $return[] = $attribute;
        }
        return $return;
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
        $previousHandle = $key->getAttributeKeyHandle();

        $loader = new StandardRequestLoader();
        $loader->load($key, $request);

        $controller = $key->getController();
        $settings = $controller->saveKey($request->request->all());
        if (!is_object($settings)) {
            $settings = $controller->getAttributeKeySettings();
        }
        $settings->setAttributeKey($key);
        $this->entityManager->persist($settings);
        $this->entityManager->flush();

        $key->setAttributeKeySettings($settings);


        // Modify the category's search indexer.
        $indexer = $this->getSearchIndexer();
        if (is_object($indexer)) {
            $indexer->updateRepositoryColumns($this, $key, $previousHandle);
        }

        $this->entityManager->persist($key);
        $this->entityManager->flush();

        $this->clearAttributeSet($key);
        if ($request->request->has('asID') && $request->request->get('asID')) {
            $key->setAttributeSet(Set::getByID($request->request->get('asID')));
        }

        $this->entityManager->flush();

        return $key;
    }

    public function import(Type $type, \SimpleXMLElement $element, Package $package = null)
    {
        $loader = new StandardImportLoader();
        $key = new LegacyKey();
        $key->setAttributeType($type);
        $key->setAttributeCategoryEntity($this->getCategoryEntity());
        if (is_object($package)) {
            $key->setPackage($package);
        }
        $loader->load($key, $element);

        $controller = $type->getController();
        $settings = $controller->importKey($element);
        if (!is_object($settings)) {
            $settings = $controller->getAttributeKeySettings();
        }

        $this->entityManager->persist($key);
        $this->entityManager->flush();

        $settings->setAttributeKey($key);
        $key->setAttributeKeySettings($settings);

        $this->entityManager->persist($settings);
        $this->entityManager->flush();

        // Modify the category's search indexer.
        $indexer = $this->getSearchIndexer();
        if (is_object($indexer)) {
            $indexer->updateRepositoryColumns($this, $key);
        }

        $this->entityManager->persist($key);
        $this->entityManager->flush();

    }

    public function getAttributeKeyByID($akID)
    {
        // TODO: Implement getAttributeKeyByID() method.
    }

    public function deleteKey(Key $key)
    {
        $values = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\Value\Value')
            ->findBy(['attribute_key' => $key]);
        $controller = $key->getController();

        foreach($values as $genericValue) {

            $legacyValue = new LegacyValue();
            $legacyValue->setGenericValue($genericValue);
            $legacyValue->setAttributeKey($key);

            $controller->setAttributeValue($legacyValue);
            $controller->deleteValue();

            $value = $legacyValue->getValueObject();
            if (is_object($value)) {
                $this->entityManager->remove($value);
                $this->entityManager->flush();
            }

            $this->entityManager->remove($genericValue);
            $this->entityManager->flush();
        }
    }

    public function deleteValue(AttributeValueInterface $value)
    {
        // TODO: Implement deleteValue() method.
    }

    public function getAttributeValue(Key $key, $mixed)
    {
        // TODO: Implement getAttributeValue() method.
    }

    protected function clearAttributeSet(Key $key)
    {
        $query = $this->entityManager->createQuery('delete from \Concrete\Core\Entity\Attribute\SetKey sk where sk.attribute_key = :key');
        $query->setParameter('key', $key);
        $query->execute();
    }

    public function addAttributeKey($type, $args, $pkg = false)
    {
        if (!is_object($type)) {
            $type = \Concrete\Core\Attribute\Type::getByHandle($type);
        }

        $controller = $type->getController();
        $settings = $controller->saveKey($args);
        if (!is_object($settings)) {
            $settings = $controller->getAttributeKeySettings();
        }
        // $key is actually an array.
        $handle = $args['akHandle'];
        $name = $args['akName'];
        $key = new LegacyKey();
        $key->setAttributeKeyHandle($handle);
        $key->setAttributeKeyName($name);
        $key->setAttributeType($type);
        $this->entityManager->persist($key);
        $this->entityManager->flush();

        $settings->setAttributeKey($key);

        $this->entityManager->persist($settings);
        $this->entityManager->flush();

        $key->setAttributeKeySettings($settings);
        $key->setAttributeCategoryEntity($this->getCategoryEntity());

        if (is_object($pkg)) {
            $key->setPackage($pkg);
        }

       // Modify the category's search indexer.
        $indexer = $this->getSearchIndexer();
        if (is_object($indexer)) {
            $indexer->updateRepositoryColumns($this, $key);
        }

        $this->entityManager->persist($key);
        $this->entityManager->flush();

        $this->clearAttributeSet($key);
        if (isset($args['asID']) && $args['asID'] > 0) {
            $key->setAttributeSet(Set::getByID($args['asID']));
        }

        $this->entityManager->flush();

        return $key;
    }



}
