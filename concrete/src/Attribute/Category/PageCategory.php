<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\PageKey;

/**
 * The attribute keys of this category are \Concrete\Core\Entity\Attribute\Key\PageKey instances.
 *
 * @method \Concrete\Core\Entity\Attribute\Key\PageKey|null getAttributeKeyByID(int $akID)
 * @method \Concrete\Core\Entity\Attribute\Key\PageKey|null getAttributeKeyByHandle(string $akHandle)
 * @method \Concrete\Core\Entity\Attribute\Key\PageKey|null getByID(int $akID)
 * @method \Concrete\Core\Entity\Attribute\Key\PageKey|null getByHandle(string $akHandle)
 * @method \Concrete\Core\Entity\Attribute\Key\PageKey[] getList()
 * @method \Concrete\Core\Entity\Attribute\Key\PageKey[] getSearchableList()
 * @method \Concrete\Core\Entity\Attribute\Key\PageKey[] getSearchableIndexedList()
 * @method \Concrete\Core\Entity\Attribute\Key\PageKey addFromRequest(\Concrete\Core\Entity\Attribute\Type $type, \Symfony\Component\HttpFoundation\Request $request)
 * @method \Concrete\Core\Entity\Attribute\Key\PageKey import(\Concrete\Core\Entity\Attribute\Type $type, \SimpleXMLElement $element, ?\Concrete\Core\Entity\Package $package = null)
 * @method \Concrete\Core\Entity\Attribute\Key\PageKey updateFromRequest(\Concrete\Core\Entity\Attribute\Key\PageKey $key, \Symfony\Component\HttpFoundation\Request $request)
 */
class PageCategory extends AbstractStandardCategory
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::createAttributeKey()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\PageKey
     */
    public function createAttributeKey()
    {
        return new PageKey();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface::getIndexedSearchTable()
     */
    public function getIndexedSearchTable()
    {
        return 'CollectionSearchIndexAttributes';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface::getIndexedSearchPrimaryKeyValue()
     *
     * @param \Concrete\Core\Page\Collection\Version\Version|\Concrete\Core\Page\Collection\Collection $mixed
     *
     * @return int
     */
    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return $mixed->getCollectionID();
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
                    'name' => 'cID',
                    'type' => 'integer',
                    'options' => ['unsigned' => true, 'notnull' => true],
                ],
            ],
            'primary' => ['cID'],
            'foreignKeys' => [
                [
                    'foreignTable' => 'Collections',
                    'localColumns' => ['cID'],
                    'foreignColumns' => ['cID'],
                    'onUpdate' => 'CASCADE',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'index' => ['exclude_page_list', 'is_featured'],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getAttributeKeyRepository()
     */
    public function getAttributeKeyRepository()
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\Key\PageKey');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getAttributeValueRepository()
     */
    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\Value\PageValue');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getAttributeValues()
     *
     * @param \Concrete\Core\Page\Collection\Version\Version|\Concrete\Core\Page\Collection\Collection $page
     *
     * @return \Concrete\Core\Entity\Attribute\Value\PageValue[]
     */
    public function getAttributeValues($page)
    {
        return $this->getAttributeValueRepository()->findBy([
            'cID' => $page->getCollectionID(),
            'cvID' => $page->getVersionID(),
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getAttributeValue()
     *
     * @param \Concrete\Core\Entity\Attribute\Key\PageKey $key
     * @param \Concrete\Core\Page\Collection\Version\Version|\Concrete\Core\Page\Collection\Collection $page
     *
     * @return \Concrete\Core\Entity\Attribute\Value\PageValue|null
     */
    public function getAttributeValue(Key $key, $page)
    {
        $cacheKey = sprintf('attribute/value/%s/page/%d/%d', $key->getAttributeKeyHandle(), $page->getCollectionID(), $page->getVersionID());
        $parameters = [
            'cID' => $page->getCollectionID(),
            'cvID' => $page->getVersionID(),
            'attribute_key' => $key,
        ];

        return $this->getAttributeValueEntity($cacheKey, $parameters);
    }
}
