<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Attribute\Key\ImportLoader\StandardImportLoader;
use Concrete\Core\Attribute\Key\RequestLoader\StandardRequestLoader;
use Concrete\Core\Entity\Attribute\Key\Key;
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
    abstract public function getAttributeRepository();
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
        return $this->getAttributeRepository()->findBy(array(
            'akIsSearchable' => true,
            'akIsInternal' => false,
        ));
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
    }

    public function add($key_type, $key, $pkg = null)
    {
        /*
         * Note: Do not type hint $pkg because old versions might not send the right object in.
         * LEGACY SUPPORT
         */
        if (is_string($key_type)) {
            $key_type = \Concrete\Core\Attribute\Type::getByHandle($key_type);
        }
        if ($key_type instanceof \Concrete\Core\Entity\Attribute\Type) {
            $key_type = $key_type->getController()->getAttributeKeyType();
            if (is_array($key)) {
                $handle = $key['akHandle'];
                $name = $key['akName'];
                $key = $this->createAttributeKey();
                $key->setAttributeKeyHandle($handle);
                $key->setAttributeKeyName($name);
            }
        }
        /* end legacy support */

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

        $this->entityManager->persist($key);
        $this->entityManager->flush();

        $controller->setAttributeKey($key);
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

    public function deleteValue(AttributeValueInterface $attribute)
    {
        // Handle legacy attributes with these three lines.
        $controller = $attribute->getAttributeKey()->getController();
        $controller->setAttributeValue($attribute);
        $controller->deleteValue();

        /*
         * @var Value
         */
        $value = $attribute->getValueObject();
        if (is_object($value)) {
            $this->entityManager->remove($attribute);
            $this->entityManager->flush();
            $this->entityManager->refresh($value);
            if (count($value->getAttributeValues()) < 1) {
                $this->entityManager->remove($value);
            }
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
