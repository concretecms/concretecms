<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\ExpressSetManager;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Package;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class ExpressCategory extends AbstractStandardCategory
{
    /**
     * The Express entity owning this attribute category.
     *
     * @var \Concrete\Core\Entity\Express\Entity
     */
    protected $expressEntity;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Entity\Express\Entity $entity the Express entity owning this attribute category
     * @param Application $application
     * @param EntityManager $entityManager
     */
    public function __construct(Entity $entity, Application $application, EntityManager $entityManager)
    {
        $this->expressEntity = $entity;
        parent::__construct($application, $entityManager);
        $this->setCategoryEntity(
            $entityManager->getRepository(Category::class)->findOneBy(['akCategoryHandle' => 'express'])
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface::getIndexedSearchTable()
     */
    public function getIndexedSearchTable()
    {
        return camelcase($this->expressEntity->getHandle()) . 'ExpressSearchIndexAttributes';
    }

    /**
     * Get the Express entity owning this attribute category.
     *
     * @return \Concrete\Core\Entity\Express\Entity
     */
    public function getExpressEntity()
    {
        return $this->expressEntity;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getSearchIndexer()
     *
     * @return \Concrete\Core\Attribute\Category\SearchIndexer\ExpressSearchIndexer
     */
    public function getSearchIndexer()
    {
        $indexer = $this->application->make('Concrete\Core\Attribute\Category\SearchIndexer\ExpressSearchIndexer');

        return $indexer;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface::getIndexedSearchPrimaryKeyValue()
     *
     * @param \Concrete\Core\Entity\Express\Entry $mixed
     */
    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return $mixed->getID();
    }

    /**
     * @return string
     */
    public function getCacheNamespace()
    {
        if ($this->expressEntity && $this->expressEntity->getId()) {
            // If app(ExpressCategory::class) is run WITHOUT specifying the entity we will just
            // merrily pass an empty one into here, which is obviously not valid. Hence the additional
            // check above for `getId()`
            return '/attribute/express/' . snake_case($this->expressEntity->getHandle());
        }
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getSearchableIndexedList()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\ExpressKey[]
     */
    public function getSearchableIndexedList()
    {
        return $this->getAttributeKeyRepository()->findBy([
            'entity' => $this->expressEntity,
            'akIsSearchableIndexed' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getSearchableList()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\ExpressKey[]
     */
    public function getSearchableList()
    {
        return $this->getAttributeKeyRepository()->findBy([
            'entity' => $this->expressEntity,
            'akIsSearchable' => true,
        ]);
    }

    public function getAttributeKeyByHandleUncached($handle)
    {
        return $this->getAttributeKeyRepository()->findOneBy(
            [
                'entity' => $this->expressEntity,
                'akHandle' => $handle,
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface::getSearchIndexFieldDefinition()
     */
    public function getSearchIndexFieldDefinition()
    {
        return [
            'columns' => [
                [
                    'name' => 'exEntryID',
                    'type' => 'integer',
                    'options' => ['unsigned' => false, 'notnull' => true],
                ],
            ],
            'primary' => ['exEntryID'],
            'foreignKeys' => [
                [
                    'foreignTable' => 'ExpressEntityEntries',
                    'localColumns' => ['exEntryID'],
                    'foreignColumns' => ['exEntryID'],
                    'onUpdate' => 'CASCADE',
                    'onDelete' => 'CASCADE',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::deleteKey()
     *
     * @param \Concrete\Core\Entity\Attribute\Key\ExpressKey $key
     */
    public function deleteKey(Key $key)
    {
        $controls = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Control\AttributeKeyControl')
            ->findBy(['attribute_key' => $key]);
        foreach ($controls as $control) {
            $this->entityManager->remove($control);
        }
        $this->entityManager->flush();

        parent::deleteKey($key);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getSetManager()
     *
     * @return \Concrete\Core\Attribute\ExpressSetManager
     */
    public function getSetManager()
    {
        if (!isset($this->setManager)) {
            $this->setManager = new ExpressSetManager($this->expressEntity, $this->entityManager);
        }

        return $this->setManager;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::createAttributeKey()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\ExpressKey
     */
    public function createAttributeKey()
    {
        return new ExpressKey();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getAttributeKeyRepository()
     */
    public function getAttributeKeyRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\ExpressKey');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getAttributeValueRepository()
     */
    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\ExpressValue');
    }

    /**
     * Does this attribute category support attribute sets?
     *
     * @return bool
     */
    public function allowAttributeSets()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getList()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\ExpressKey[]
     */
    public function getList()
    {
        return $this->getAttributeKeyRepository()->findBy(['entity' => $this->expressEntity]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getAttributeTypes()
     */
    public function getAttributeTypes()
    {
        /** @var TypeFactory $typeFactory */
        $typeFactory = $this->application->make(TypeFactory::class);
        return $typeFactory->getList("express");
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::import()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\ExpressKey
     */
    public function import(Type $type, \SimpleXMLElement $element, Package $package = null)
    {
        $key = parent::import($type, $element, $package);
        /**
         * @var $key ExpressKey
         */
        $key->setEntity($this->expressEntity);
        $key->setIsAttributeKeyUnique((string) $element['unique'] == 1);
        return $key;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::addFromRequest()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\ExpressKey
     */
    public function addFromRequest(Type $type, Request $request)
    {
        $key = parent::addFromRequest($type, $request);
        /**
         * @var $key ExpressKey
         */
        $key->setEntity($this->expressEntity);
        $this->saveFromRequest($key, $request);
        return $key;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::updateFromRequest()
     *
     * @param \Concrete\Core\Entity\Attribute\Key\UserKey $key
     *
     * @return \Concrete\Core\Entity\Attribute\Key\UserKey
     */
    public function updateFromRequest(Key $key, Request $request)
    {
        $key = parent::updateFromRequest($key, $request);

        return $this->saveFromRequest($key, $request);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getAttributeValues()
     *
     * @param \Concrete\Core\Entity\Express\Entry $entry
     *
     * @return \Concrete\Core\Entity\Attribute\Value\ExpressValue[]
     */
    public function getAttributeValues($entry)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\ExpressValue');
        $values = $r->findBy([
            'entry' => $entry,
        ]);

        return $values;
    }

    /**
     * @param \Concrete\Core\Entity\Attribute\Key\ExpressKey $key The user attribute key to be updated
     * @param \Symfony\Component\HttpFoundation\Request $request The request containing the posted data
     *
     * @return \Concrete\Core\Entity\Attribute\Key\ExpressKey
     */
    protected function saveFromRequest(Key $key, Request $request)
    {
        $key->setIsAttributeKeyUnique((string) $request->request->get('eakUnique') == 1);
        // Actually save the changes to the database
        $this->entityManager->flush();
        return $key;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getAttributeValue()
     *
     * @param \Concrete\Core\Entity\Attribute\Key\ExpressKey $key
     * @param \Concrete\Core\Entity\Express\Entry $entry
     *
     * @return \Concrete\Core\Entity\Attribute\Value\ExpressValue|null
     */
    public function getAttributeValue(Key $key, $entry)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\ExpressValue');
        $value = $r->findOneBy([
            'entry' => $entry,
            'attribute_key' => $key,
        ]);

        return $value;
    }
}
