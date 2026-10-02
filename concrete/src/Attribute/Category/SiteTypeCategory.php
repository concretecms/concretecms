<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\SiteKey;
use Concrete\Core\Entity\Attribute\Value\SiteTypeValue;
use Concrete\Core\Entity\Attribute\Value\SiteValue;
use Concrete\Core\Entity\Site\Site;

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
class SiteTypeCategory extends AbstractStandardCategory
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

    public function getSearchIndexer()
    {
        return null;
    }

    public function getIndexedSearchTable()
    {
        return false;
    }

    /**
     * @param $mixed Site
     *
     * @return mixed
     */
    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return false;
    }

    public function getSearchIndexFieldDefinition()
    {
        return false;
    }

    public function getAttributeKeyRepository()
    {
        return $this->entityManager->getRepository(SiteKey::class);
    }

    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository(SiteTypeValue::class);
    }

    public function getAttributeValues($skeleton)
    {
        $r = $this->entityManager->getRepository(SiteTypeValue::class);

        return $r->findBy([
            'skeleton' => $skeleton,
        ]);
    }

    /**
     * @param Key $key
     * @param \Concrete\Core\Entity\Site\Skeleton $skeleton
     */
    public function getAttributeValue(Key $key, $skeleton)
    {
        $cacheKey = sprintf('attribute/value/%s/sitetype/%d', $key->getAttributeKeyHandle(), $skeleton->getSiteSkeletonID());
        $parameters = [
            'skeleton' => $skeleton,
            'attribute_key' => $key,
        ];

        return $this->getAttributeValueEntity($cacheKey, $parameters);
    }
}
