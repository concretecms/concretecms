<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexer;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Attribute\Key\ImportLoader\StandardImportLoader;
use Concrete\Core\Attribute\Key\RequestLoader\StandardRequestLoader;
use Concrete\Core\Attribute\SetFactory;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Type as AttributeType;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManager;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\Request;

/**
 * Abstract class to be used by attribute category classes.
 */
abstract class AbstractCategory implements CategoryInterface, StandardSearchIndexerInterface
{
    /**
     * The EntityManager instance.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @ignore It seems that this is not used.
     */
    protected $entity;

    /**
     * The Application instance.
     *
     * @var Application
     */
    protected $application;

    /**
     * The instance of the SetManagerInterface (if set).
     *
     * @var \Concrete\Core\Attribute\SetManagerInterface|null
     */
    protected $setManager;

    /**
     * Initialize the instance.
     *
     * @param Application $application the Application instance
     * @param EntityManager $entityManager the EntityManager instance
     */
    public function __construct(Application $application, EntityManager $entityManager)
    {
        $this->application = $application;
        $this->entityManager = $entityManager;
    }

    /**
     * Get the repository for the attribute keys.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    abstract public function getAttributeKeyRepository();

    /**
     * Get the repository for the attribute values.
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    abstract public function getAttributeValueRepository();

    /**
     * Create a new attribute key.
     *
     * @return \Concrete\Core\Entity\Attribute\Key\Key
     */
    abstract public function createAttributeKey();

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getSearchIndexer()
     */
    public function getSearchIndexer()
    {
        $indexer = $this->application->make(StandardSearchIndexer::class);

        return $indexer;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getList()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\Key[]
     */
    public function getList()
    {
        return $this->getAttributeKeyRepository()->findBy([
            'akIsInternal' => false,
        ]);
    }

    /**
     * Get the list of attribute keys that are searchable.
     *
     * @return \Concrete\Core\Entity\Attribute\Key\Key[]
     */
    public function getSearchableList()
    {
        return $this->getAttributeKeyRepository()->findBy([
            'akIsSearchable' => true,
        ]);
    }

    /**
     * Get the list of attribute keys that are searchable and indexed.
     *
     * @return \Concrete\Core\Entity\Attribute\Key\Key[]
     */
    public function getSearchableIndexedList()
    {
        return $this->getAttributeKeyRepository()->findBy([
            'akIsSearchableIndexed' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getAttributeKeyByHandle()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\Key|null
     */
    public function getAttributeKeyByHandle($handle)
    {
        $cache = $this->application->make('cache/request');
        $class = substr(get_class($this), strrpos(get_class($this), '\\') + 1);
        $category = strtolower(substr($class, 0, strpos($class, 'Category')));
        $item = $cache->getItem(sprintf('/attribute/%s/handle/%s', $category, $handle));
        if (!$item->isMiss()) {
            $key = $item->get();
        } else {
            $key = $this->getAttributeKeyRepository()->findOneBy([
                'akHandle' => $handle,
            ]);
            $cache->save($item->set($key));
        }

        return $key;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getAttributeKeyByID()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\Key|null
     */
    public function getAttributeKeyByID($akID)
    {
        $cache = $this->application->make('cache/request');
        $class = substr(get_class($this), strrpos(get_class($this), '\\') + 1);
        $category = strtolower(substr($class, 0, strpos($class, 'Category')));
        $item = $cache->getItem(sprintf('/attribute/%s/id/%s', $category, $akID));
        if (!$item->isMiss()) {
            $key = $item->get();
        } else {
            $key = $this->getAttributeKeyRepository()->findOneBy([
                'akID' => $akID,
            ]);
            $cache->save($item->set($key));
        }

        return $key;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::delete()
     */
    public function delete()
    {
        $keys = $this->getList();
        foreach ($keys as $key) {
            $this->entityManager->remove($key);
        }
        $this->entityManager->flush();
    }

    /**
     * Add a new attribute key.
     *
     * @param \Concrete\Core\Entity\Attribute\Type|string $type the attribute type (or its handle)
     * @param \Concrete\Core\Entity\Attribute\Key\Key|array $key an empty attribute key, or an array with keys 'akHandle' (the attribute key handle), 'akName' (the attribute key name) and optionally 'asID' (the ID of the attribute set)
     * @param \Concrete\Core\Entity\Attribute\Key\Settings\Settings|null $settings the attribute key settings (if not specified, a new settings instance will be created)
     * @param \Concrete\Core\Entity\Package|null $pkg the entity of the package that's creating the attribute key
     *
     * @return \Concrete\Core\Entity\Attribute\Key\Key
     */
    public function add($type, $key, $settings = null, $pkg = null)
    {
        if (is_string($type)) {
            $typeFactory = $this->application->make(TypeFactory::class);
            /* @var TypeFactory $typeFactory */
            $type = $typeFactory->getByHandle($type);
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
            $settings = null;
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
            if ($set !== null) {
                $manager->addKey($set, $key);
            }
        }

        return $key;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::addFromRequest()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\Key
     */
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

    /**
     * Import a new attribute key from a SimpleXMLElement instance.
     *
     * @param AttributeType $type the type of the attribute key to be created
     * @param SimpleXMLElement $element the SimpleXMLElement instance containing the data of the attribute key to be created
     * @param Package|null $package the entity of the package that's creating the attribute key (if applicable)
     *
     * @return \Concrete\Core\Entity\Attribute\Key\Key
     */
    public function import(AttributeType $type, SimpleXMLElement $element, Package $package = null)
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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::updateFromRequest()
     */
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
     * Get the EntityManager instance.
     *
     * @param EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::deleteKey()
     */
    public function deleteKey(Key $key)
    {
        // Delete any attribute values found attached to this key
        $values = $this->getAttributeValueRepository()->findBy(['attribute_key' => $key]);
        foreach ($values as $attributeValue) {
            $this->deleteValue($attributeValue);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::deleteValue()
     */
    public function deleteValue(AttributeValueInterface $attributeValue)
    {
        /* @var \Concrete\Core\Entity\Attribute\Value\AbstractValue $attributeValue */

        $genericValue = $attributeValue->getGenericValue();
        if ($genericValue !== null) {
            $genericValues = $this->getAttributeValueRepository()->findBy(['generic_value' => $genericValue]);
            if (count($genericValues) == 1) {

                // Handle legacy attributes with these three lines.
                $controller = $attributeValue->getAttributeKey()->getController();
                $controller->setAttributeValue($attributeValue);
                $controller->deleteValue();

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

    /**
     * Get the object to be used to update attribute keys with the data contained in a Symfony\Component\HttpFoundation\Request instance.
     *
     * @return \Concrete\Core\Attribute\Key\RequestLoader\RequestLoaderInterface
     */
    public function getRequestLoader()
    {
        return new StandardRequestLoader();
    }

    /**
     * Get the object to be used to update attribute keys with the data contained in a SimpleXMLElement instance.
     *
     * @return \Concrete\Core\Attribute\Key\ImportLoader\ImportLoaderInterface
     */
    public function getImportLoader()
    {
        return new StandardImportLoader();
    }

    /**
     * @param int $akID
     *
     * @return \Concrete\Core\Entity\Attribute\Key\Key|null
     *
     * @deprecated use the getAttributeKeyByID method
     */
    public function getByID($akID)
    {
        return $this->getAttributeKeyByID($akID);
    }

    /**
     * @param string $akHandle
     *
     * @return \Concrete\Core\Entity\Attribute\Key\Key|null
     *
     * @deprecated use the getAttributeKeyByHandle method
     */
    public function getByHandle($akHandle)
    {
        return $this->getAttributeKeyByHandle($akHandle);
    }
}
