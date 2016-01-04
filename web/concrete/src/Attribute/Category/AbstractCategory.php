<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Controller\Element\Attribute\Header;
use Concrete\Controller\Element\Attribute\KeyList;
use Concrete\Controller\Element\Attribute\StandardListHeader;
use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\AttributeInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Attribute\EntityInterface;
use Concrete\Core\Attribute\Key\Factory;
use Concrete\Core\Attribute\SetFactory;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\Attribute\Set;
use Concrete\Core\Entity\Attribute\Key\Key as AttributeKey;
use Concrete\Core\Entity\Attribute\SetKey;
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

    public function __construct(Application $application, EntityManager $entityManager)
    {
        $this->application = $application;
        $this->entityManager = $entityManager;
    }

    public function getAttributeTypes()
    {
        return $this->getCategoryEntity()->getAttributeTypes();
    }

    public function getList()
    {
        return $this->getAttributeRepository()->findAll();
    }

    public function getSearchableList()
    {
        $query = $this->getAttributeRepository()->createQueryBuilder('a');
        $query->join('a.attribute_key', 'ta');
        $query->andWhere('ta.akIsSearchable = true');
        return $query->getQuery()->getResult();
    }

    public function getSearchableIndexedList()
    {
        $query = $this->getAttributeRepository()->createQueryBuilder('a');
        $query->join('a.attribute_key', 'ta');
        $query->andWhere('ta.akIsSearchableIndexed = true');
        return $query->getQuery()->getResult();
    }

    public function getAttributeKeyByHandle($handle)
    {
        $query = $this->getAttributeRepository()->createQueryBuilder('a');
        $query->join('a.attribute_key', 'ta');
        $query->andWhere('ta.akHandle = :akHandle');
        $query->setParameter('akHandle', $handle);
        return $query->getQuery()->getOneOrNullResult();
    }

    public function getAttributeKeyByID($akID)
    {
        $query = $this->getAttributeRepository()->createQueryBuilder('a');
        $query->join('a.attribute_key', 'ta');
        $query->andWhere('ta.akID = :akID');
        $query->setParameter('akID', $akID);
        return $query->getQuery()->getOneOrNullResult();
    }

    // Create
    public function addFromRequest(AttributeType $type, Request $request)
    {
        $key = $type->getController()->createAttributeKey();
        $loader = $key->getRequestLoader();
        $loader->load($key, $request);

        // Modify the category's search indexer.
        $indexer = $this->getCategoryEntity()
            ->getController()->getSearchIndexer();
        $indexer->updateTable($this, $key);

        return $key;
    }

    public function import(AttributeType $type, \SimpleXMLElement $element)
    {
        $key = $type->getController()->createAttributeKey();
        $loader = $key->getImportLoader();
        $loader->load($key, $element);

        // Modify the category's search indexer.
        $indexer = $this->getCategoryEntity()
            ->getController()->getSearchIndexer();
        $indexer->updateTable($this, $key);

        return $key;
    }


    // Update
    public function updateFromRequest(AttributeInterface $attribute, Request $request)
    {
        $key = $attribute->getAttributeKey();
        $loader = $key->getRequestLoader();
        $loader->load($key, $request);
        $attribute->setAttributeKey($key);
        return $attribute;
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

    public function delete(AttributeInterface $attribute)
    {
        // Delete from any attribute sets
        $key = $attribute->getAttributeKey();
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\SetKey');
        $setKeys = $r->findBy(array('attribute_key' => $key));
        foreach($setKeys as $setKey) {
            $this->entityManager->remove($setKey);
        }
        $this->entityManager->remove($key);

        $this->entityManager->remove($attribute);
        $this->entityManager->flush();
    }

    public function associateAttributeKeyType(AttributeType $type)
    {
        $this->getCategoryEntity()->getAttributeTypes()->add($type);
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

    public function getUngroupedAttributes()
    {
        $attributes = array();
        foreach($this->getList() as $attribute) {
            $key = $attribute->getAttributeKey();
            $query = $this->entityManager->createQuery(
                'select sk from \Concrete\Core\Entity\Attribute\SetKey sk where sk.attribute_key = :key'
            );
            $query->setParameter('key', $key);
            $r = $query->getOneOrNullResult();
            if (!is_object($r)) {
                $attributes[] = $attribute;
            }
        }
        return $attributes;
    }


}
