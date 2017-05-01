<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Attribute\Key\ImportLoader\StandardImportLoader;
use Concrete\Core\Attribute\Key\RequestLoader\StandardRequestLoader;
use Concrete\Core\Attribute\Set;
use Concrete\Core\Attribute\SetFactory;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\Settings\Settings;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Attribute\Type as AttributeType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractCategory implements CategoryInterface, StandardSearchIndexerInterface
{
    protected $entityManager;
    protected $entity;
    protected $application;
    protected $setManager;

    public function __construct(Application $application, EntityManager $entityManager)
    {
        $this->application = $application;
        $this->entityManager = $entityManager;
    }

    public function getSearchIndexer()
    {
        $indexer = $this->application->make('Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexer');

        return $indexer;
    }

    /**
     * @return EntityRepository
     */
    abstract public function getAttributeKeyRepository();
    abstract public function getAttributeValueRepository();
    abstract public function createAttributeKey();

    public function getByID($akID)
    {
        return $this->getAttributeKeyByID($akID);
    }

    public function getByHandle($akHandle)
    {
        return $this->getAttributeKeyByHandle($akHandle);
    }


    public function getList()
    {
        return $this->getAttributeKeyRepository()->findBy(array(
            'akIsInternal' => false,
        ));
    }

    public function getSearchableList()
    {
        return $this->getAttributeKeyRepository()->findBy(array(
            'akIsSearchable' => true,
        ));
    }

    public function getSearchableIndexedList()
    {
        return $this->getAttributeKeyRepository()->findBy(array(
            'akIsSearchableIndexed' => true,
        ));
    }

    public function getAttributeKeyByHandle($handle)
    {
        $cache = $this->application->make("cache/request");
        $class = substr(get_class($this), strrpos(get_class($this), '\\') + 1);
        $category = strtolower(substr($class, 0, strpos($class, 'Category')));
        $item = $cache->getItem(sprintf('/attribute/%s/handle/%s', $category, $handle));
        if (!$item->isMiss()) {
            $key = $item->get();
        } else {
            $key = $this->getAttributeKeyRepository()->findOneBy(array(
                'akHandle' => $handle,
            ));
            $cache->save($item->set($key));
        }
        return $key;
    }

    public function getAttributeKeyByID($akID)
    {
        $cache = $this->application->make("cache/request");
        $class = substr(get_class($this), strrpos(get_class($this), '\\') + 1);
        $category = strtolower(substr($class, 0, strpos($class, 'Category')));
        $item = $cache->getItem(sprintf('/attribute/%s/id/%s', $category, $akID));
        if (!$item->isMiss()) {
            $key = $item->get();
        } else {
            $key = $this->getAttributeKeyRepository()->findOneBy(array(
                'akID' => $akID,
            ));
            $cache->save($item->set($key));
        }
        return $key;
    }

    public function delete()
    {
        $keys = $this->getList();
        foreach($keys as $key) {
            $this->entityManager->remove($key);
        }
        $this->entityManager->flush();
    }

    public function add($type, $key, $settings = null, $pkg = null)
    {

        if (is_string($type)) {
            $type = \Concrete\Core\Attribute\Type::getByHandle($type);
        }

        // Legacy array support for $key
        $asID = false;
        if (is_array($key)) {
            $handle = $key['akHandle'];
            $name = $key['akName'];
            if (isset($key['asID'])) {
                $asID = $key['asID'];
            }
            $key = $this->createAttributeKey();
            $key->setAttributeKeyHandle($handle);
            $key->setAttributeKeyName($name);
        }

        // Legacy support for third parameter which used to be package
        if ($settings instanceof Package || $settings instanceof \Concrete\Core\Package\Package) {
            $pkg = $settings;
            unset($settings);
        }

        if (!$settings) {
            $settings = $type->getController()->getAttributeKeySettings();
        }

        $key->setAttributeType($type);
        $this->entityManager->persist($key);
        $this->entityManager->flush();

        $settings->setAttributeKey($key);
        $key->setAttributeKeySettings($settings);

        $this->entityManager->persist($settings);
        $this->entityManager->flush();

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

        /* legacy support, attribute set */

        if ($asID) {
            $manager = $this->getSetManager();
            $factory = new SetFactory($this->entityManager);
            $set = $factory->getByID($asID);
            if (is_object($set)) {
                $manager->addKey($set, $key);
            }
        }

        return $key;
    }

    public function addFromRequest(AttributeType $type, Request $request)
    {
        $key = $this->createAttributeKey();
        $loader = $this->getRequestLoader();
        $loader->load($key, $request);

        $controller = $type->getController();

        $this->entityManager->persist($key);
        $this->entityManager->flush();

        $controller->setAttributeKey($key);
        $settings = $controller->saveKey($request->request->all());
        if (!is_object($settings)) {
            $settings = $controller->getAttributeKeySettings();
        }
        return $this->add($type, $key, $settings);
    }

    public function import(AttributeType $type, \SimpleXMLElement $element, Package $package = null)
    {
        $key = $this->createAttributeKey();
        $loader = $this->getImportLoader();
        $loader->load($key, $element);

        $controller = $type->getController();
        $settings = $controller->importKey($element);
        if (!is_object($settings)) {
            $settings = $controller->getAttributeKeySettings();
        }
        return $this->add($type, $key, $settings, $package);
    }

    // Update
    public function updateFromRequest(Key $key, Request $request)
    {
        $previousHandle = $key->getAttributeKeyHandle();

        $loader = $this->getRequestLoader();
        $loader->load($key, $request);

        $controller = $key->getController();
        $settings = $controller->saveKey($request->request->all());
        if (!is_object($settings)) {
            $settings = $controller->getAttributeKeySettings();
        }
        $settings->setAttributeKey($key);

        $this->entityManager->persist($settings);
        $this->entityManager->flush();

        // Modify the category's search indexer.
        $indexer = $this->getSearchIndexer();
        if (is_object($indexer)) {
            $indexer->updateRepositoryColumns($this, $key, $previousHandle);
        }

        $this->entityManager->persist($key);
        $this->entityManager->flush();

        return $key;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function deleteKey(Key $key)
    {
        // Delete any attribute values found attached to this key
        $values = $this->getAttributeValueRepository()->findBy(['attribute_key' => $key]);
        foreach($values as $attributeValue) {
            $this->deleteValue($attributeValue);
        }
    }

    public function deleteValue(AttributeValueInterface $attributeValue)
    {
        // Handle legacy attributes with these three lines.
        $controller = $attributeValue->getAttributeKey()->getController();
        $controller->setAttributeValue($attributeValue);
        $controller->deleteValue();

        $genericValue = $attributeValue->getGenericValue();
        if (is_object($genericValue)) {
            $genericValues = $this->getAttributeValueRepository()->findBy(['generic_value' => $genericValue]);
            if (count($genericValues) == 1) {
                $value = $attributeValue->getValueObject();
                if (is_object($value)) {
                    $this->entityManager->remove($value);
                    $this->entityManager->flush();
                }
                $this->entityManager->remove($genericValue);
            }
            $this->entityManager->remove($attributeValue);
        }

        $this->entityManager->flush();
    }

    public function getRequestLoader()
    {
        return new StandardRequestLoader();
    }

    public function getImportLoader()
    {
        return new StandardImportLoader();
    }
}
