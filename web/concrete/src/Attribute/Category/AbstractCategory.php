<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Controller;
use Concrete\Core\Attribute\EntityInterface;
use Concrete\Core\Attribute\Key\ImportLoader\StandardImportLoader;
use Concrete\Core\Attribute\Key\RequestLoader\StandardRequestLoader;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\Type\Type;
use Concrete\Core\Entity\Attribute\Set;
use Concrete\Core\Entity\Attribute\Value\Value\Value;
use Concrete\Core\Entity\Package;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Concrete\Core\Entity\Attribute\Type as AttributeType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractCategory implements CategoryInterface
{
    protected $entityManager;
    protected $entity;
    protected $categoryEntity;
    protected $application;

    /**
     * @return EntityRepository
     */
    abstract public function getAttributeRepository();

    public function getByID($akID)
    {
        return $this->getAttributeKeyByID($akID);
    }

    public function getByHandle($akHandle)
    {
        return $this->getAttributeKeyByHandle($akHandle);
    }

    public function __construct(Application $application, EntityManager $entityManager)
    {
        $this->application = $application;
        $this->entityManager = $entityManager;
    }

    public function getAttributeTypes()
    {
        return $this->getCategoryEntity()->getAttributeTypes();
    }

    public function allowAttributeSets()
    {
        return $this->getCategoryEntity()->allowAttributeSets();
    }

    public function getList()
    {
        return $this->getAttributeRepository()->findAll();
    }

    public function getSearchableList()
    {
        return $this->getAttributeRepository()->findBy(array(
            'akIsSearchable' => true,
        ));
    }

    public function getSearchableIndexedList()
    {
        return $this->getAttributeRepository()->findBy(array(
            'akIsSearchableIndexed' => true,
        ));
    }

    public function getAttributeKeyByHandle($handle)
    {
        return $this->getAttributeRepository()->findOneBy(array(
            'akHandle' => $handle,
        ));
    }

    public function getAttributeKeyByID($akID)
    {
        return $this->getAttributeRepository()->findOneBy(array(
            'akID' => $akID,
        ));
    }

    public function delete()
    {
        $keys = $this->getList();
        foreach($keys as $key) {
            $this->entityManager->remove($key);
        }
        $this->entityManager->flush();

        $this->entityManager->remove($this->getCategoryEntity());
        $this->entityManager->flush();

    }

    public function add($key_type, $key, Package $pkg = null)
    {
        /*
         * LEGACY SUPPORT
         */
        if ($key_type instanceof \Concrete\Core\Entity\Attribute\Type) {
            $type_handle = $key_type->getAttributeTypeHandle();
            $key_type = $key_type->getController()->getAttributeKeyType();
            // $key is actually an array.
            $handle = $key['akHandle'];
            $name = $key['akName'];
            $key = $this->createAttributeKey();
            $key->setAttributeKeyHandle($handle);
            $key->setAttributeKeyName($name);
        }
        $key_type->setAttributeKey($key);
        $key->setAttributeKeyType($key_type);

        if (is_object($pkg)) {
            $key->setPackage($pkg);
        }
        // Modify the category's search indexer.
        $indexer = $this->getSearchIndexer();
        if (is_object($indexer)) {
            $indexer->updateRepository($this, $key);
        }

        $this->entityManager->persist($key);
        $this->entityManager->flush();

        return $key;
    }

    public function addFromRequest(AttributeType $type, Request $request)
    {
        $key = $this->createAttributeKey();
        $loader = $this->getRequestLoader();
        $loader->load($key, $request);

        $controller = $type->getController();

        $key_type = $controller->saveKey($request->request->all());
        if (!is_object($key_type)) {
            $key_type = $controller->getAttributeKeyType();
        }
        return $this->add($key_type, $key);
    }

    public function import(AttributeType $type, \SimpleXMLElement $element, Package $package = null)
    {
        $key = $this->createAttributeKey();
        $loader = $this->getImportLoader();
        $loader->load($key, $element);

        $controller = $type->getController();
        $key_type = $controller->importKey($element);
        if (!is_object($key_type)) {
            $key_type = $controller->getAttributeKeyType();
        }
        return $this->add($key_type, $key, $package);
    }

    // Update
    public function updateFromRequest(Key $key, Request $request)
    {
        $previousHandle = $key->getAttributeKeyHandle();

        $loader = $this->getRequestLoader();
        $loader->load($key, $request);

        $controller = $key->getController();
        $key_type = $controller->saveKey($request->request->all());
        if (!is_object($key_type)) {
            $key_type = $controller->getAttributeKeyType();
        }
        $key_type->setAttributeKey($key);
        $key->setAttributeKeyType($key_type);

        // Modify the category's search indexer.
        $indexer = $this->getSearchIndexer();
        if (is_object($indexer)) {
            $indexer->updateRepository($this, $key, $previousHandle);
        }

        $this->entityManager->persist($key);
        $this->entityManager->flush();

        return $key;
    }

    public function getAttributeSets()
    {
        return $this->categoryEntity->getAttributeSets();
    }

    /**
     * @return mixed
     */
    public function getCategoryEntity()
    {
        return $this->categoryEntity;
    }

    /**
     * @param mixed $categoryEntity
     */
    public function setCategoryEntity(Category $categoryEntity)
    {
        $this->categoryEntity = $categoryEntity;
    }

    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
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
        $controller = $key->getController();
        $controller->deleteKey();

        // Delete from any attribute sets
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\SetKey');
        $setKeys = $r->findBy(array('attribute_key' => $key));
        foreach ($setKeys as $setKey) {
            $this->entityManager->remove($setKey);
        }
        $this->entityManager->remove($key);

        $this->entityManager->remove($key);
        $this->entityManager->flush();
    }

    public function associateAttributeKeyType(AttributeType $type)
    {
        /**
         * @var $types ArrayCollection
         */
        $types = $this->getCategoryEntity()->getAttributeTypes();
        if (!$types->contains($type)) {
            $types->add($type);
        }
        $this->entityManager->persist($this->getCategoryEntity());
        $this->entityManager->flush();
    }

    public function addSet($handle, $name, $pkg = null, $locked = null)
    {
        $set = new Set();
        $set->setAttributeKeyCategory($this->getCategoryEntity());
        $set->setAttributeSetHandle($handle);
        $set->setAttributeSetName($name);
        $set->setAttributeSetIsLocked($locked);
        $this->entityManager->persist($set);
        $this->entityManager->flush();

        return $set;
    }

    public function getSearchIndexer()
    {
        $indexer = $this->application->make('Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexer');

        return $indexer;
    }

    public function getUnassignedAttributeKeys()
    {
        $attributes = array();
        foreach ($this->getList() as $key) {
            $query = $this->entityManager->createQuery(
                'select sk from \Concrete\Core\Entity\Attribute\SetKey sk where sk.attribute_key = :key'
            );
            $query->setParameter('key', $key);
            $r = $query->getOneOrNullResult();
            if (!is_object($r)) {
                $attributes[] = $key;
            }
        }

        return $attributes;
    }

    public function deleteValue(AttributeValueInterface $attribute)
    {
        $controller = $attribute->getAttributeKey()->getController();
        $controller->deleteValue();

        /*
         * @var Value
         */
        $value = $attribute->getValueObject();
        $this->entityManager->remove($attribute);

        $this->entityManager->flush();

        $this->entityManager->refresh($value);
        if (count($value->getAttributeValues()) < 1) {
            $this->entityManager->remove($value);
        }
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
