<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\SiteKey;

/**
 * The attribute keys of this category are \Concrete\Core\Entity\Attribute\Key\SiteKey instances.
 *
 * @method \Concrete\Core\Entity\Attribute\Key\SiteKey|null getAttributeKeyByID(int $akID)
 * @method \Concrete\Core\Entity\Attribute\Key\SiteKey|null getAttributeKeyByHandle(string $akHandle)
 * @method \Concrete\Core\Entity\Attribute\Key\SiteKey|null getByID(int $akID)
 * @method \Concrete\Core\Entity\Attribute\Key\SiteKey|null getByHandle(string $akHandle)
 * @method \Concrete\Core\Entity\Attribute\Key\SiteKey[] getList()
 * @method \Concrete\Core\Entity\Attribute\Key\SiteKey[] getSearchableList()
 * @method \Concrete\Core\Entity\Attribute\Key\SiteKey[] getSearchableIndexedList()
 * @method \Concrete\Core\Entity\Attribute\Key\SiteKey addFromRequest(\Concrete\Core\Entity\Attribute\Type $type, \Symfony\Component\HttpFoundation\Request $request)
 * @method \Concrete\Core\Entity\Attribute\Key\SiteKey import(\Concrete\Core\Entity\Attribute\Type $type, \SimpleXMLElement $element, ?\Concrete\Core\Entity\Package $package = null)
 * @method \Concrete\Core\Entity\Attribute\Key\SiteKey updateFromRequest(\Concrete\Core\Entity\Attribute\Key\SiteKey $key, \Symfony\Component\HttpFoundation\Request $request)
 */
class SiteCategory extends AbstractStandardCategory
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::createAttributeKey()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\SiteKey
     */
    public function createAttributeKey()
    {
        return new SiteKey();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface::getIndexedSearchTable()
     */
    public function getIndexedSearchTable()
    {
        return 'SiteSearchIndexAttributes';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface::getIndexedSearchPrimaryKeyValue()
     *
     * @param \Concrete\Core\Entity\Site\Site $mixed
     *
     * @return int
     */
    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return $mixed->getSiteID();
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
                    'name' => 'siteID',
                    'type' => 'integer',
                    'options' => ['unsigned' => true, 'notnull' => true],
                ],
            ],
            'primary' => ['siteID'],
            'foreignKeys' => [
                [
                    'foreignTable' => 'Sites',
                    'localColumns' => ['siteID'],
                    'foreignColumns' => ['siteID'],
                    'onUpdate' => 'CASCADE',
                    'onDelete' => 'CASCADE',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getAttributeKeyRepository()
     */
    public function getAttributeKeyRepository()
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\Key\SiteKey');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getAttributeValueRepository()
     */
    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\Value\SiteValue');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getAttributeValues()
     *
     * @param \Concrete\Core\Entity\Site\Site $site
     *
     * @return \Concrete\Core\Entity\Attribute\Value\SiteValue[]
     */
    public function getAttributeValues($site)
    {
        return $this->getAttributeValueRepository()->findBy([
            'site' => $site,
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getAttributeValue()
     *
     * @param \Concrete\Core\Entity\Attribute\Key\SiteKey $key
     * @param \Concrete\Core\Entity\Site\Site $site
     *
     * @return \Concrete\Core\Entity\Attribute\Value\SiteValue|null
     */
    public function getAttributeValue(Key $key, $site)
    {
        $cacheKey = sprintf('attribute/value/%s/site/%d', $key->getAttributeKeyHandle(), $site->getSiteID());
        $parameters = [
            'site' => $site,
            'attribute_key' => $key,
        ];

        return $this->getAttributeValueEntity($cacheKey, $parameters);
    }
}
