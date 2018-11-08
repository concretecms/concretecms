<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\SiteKey;

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
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\SiteKey');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getAttributeValueRepository()
     */
    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\SiteValue');
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
        $values = $this->getAttributeValueRepository()->findBy([
            'site' => $site,
        ]);

        return $values;
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
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\SiteValue');
        $value = $r->findOneBy([
            'site' => $site,
            'attribute_key' => $key,
        ]);

        return $value;
    }
}
