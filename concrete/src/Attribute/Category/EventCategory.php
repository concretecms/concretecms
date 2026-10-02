<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Entity\Attribute\Key\EventKey;
use Concrete\Core\Entity\Attribute\Key\Key;

/**
 * The attribute keys of this category are \Concrete\Core\Entity\Attribute\Key\EventKey instances.
 *
 * @method \Concrete\Core\Entity\Attribute\Key\EventKey|null getAttributeKeyByID(int $akID)
 * @method \Concrete\Core\Entity\Attribute\Key\EventKey|null getAttributeKeyByHandle(string $akHandle)
 * @method \Concrete\Core\Entity\Attribute\Key\EventKey|null getByID(int $akID)
 * @method \Concrete\Core\Entity\Attribute\Key\EventKey|null getByHandle(string $akHandle)
 * @method \Concrete\Core\Entity\Attribute\Key\EventKey[] getList()
 * @method \Concrete\Core\Entity\Attribute\Key\EventKey[] getSearchableList()
 * @method \Concrete\Core\Entity\Attribute\Key\EventKey[] getSearchableIndexedList()
 * @method \Concrete\Core\Entity\Attribute\Key\EventKey addFromRequest(\Concrete\Core\Entity\Attribute\Type $type, \Symfony\Component\HttpFoundation\Request $request)
 * @method \Concrete\Core\Entity\Attribute\Key\EventKey import(\Concrete\Core\Entity\Attribute\Type $type, \SimpleXMLElement $element, ?\Concrete\Core\Entity\Package $package = null)
 * @method \Concrete\Core\Entity\Attribute\Key\EventKey updateFromRequest(\Concrete\Core\Entity\Attribute\Key\EventKey $key, \Symfony\Component\HttpFoundation\Request $request)
 */
class EventCategory extends AbstractStandardCategory
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::createAttributeKey()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\EventKey
     */
    public function createAttributeKey()
    {
        return new EventKey();
    }

    public function getIndexedSearchTable()
    {
        return 'CalendarEventSearchIndexAttributes';
    }

    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return $mixed->getEvent()->getID();
    }

    public function getSearchIndexFieldDefinition()
    {
        return [
            'columns' => [
                [
                    'name' => 'eventID',
                    'type' => 'integer',
                    'options' => ['unsigned' => true, 'default' => 0, 'notnull' => true], ],
            ],
            'primary' => ['eventID'],
        ];
    }

    public function getAttributeKeyRepository()
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\Key\EventKey');
    }

    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\Value\EventValue');
    }

    public function getAttributeValues($version)
    {
        $query = $this->entityManager->createQuery('select eav from Concrete\Core\Entity\Attribute\Value\EventValue eav
          where eav.version = :version');
        $query->setParameter('version', $version);

        return $query->getResult();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getAttributeValue()
     *
     * @param \Concrete\Core\Entity\Calendar\CalendarEventVersion $version
     *
     * @return \Concrete\Core\Entity\Attribute\Value\EventValue|null
     */
    public function getAttributeValue(Key $key, $version)
    {
        $cacheKey = sprintf('attribute/value/%s/event/%d', $key->getAttributeKeyHandle(), $version->getID());
        $parameters = [
            'version' => $version,
            'attribute_key' => $key,
        ];

        return $this->getAttributeValueEntity($cacheKey, $parameters);
    }
}
