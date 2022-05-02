<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Entity\Attribute\Key\EventKey;
use Concrete\Core\Entity\Attribute\Key\Key;

class EventCategory extends AbstractStandardCategory
{
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
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\EventKey');
    }

    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\EventValue');
    }

    public function getAttributeValues($version)
    {
        $query = $this->entityManager->createQuery('select eav from Concrete\Core\Entity\Attribute\Value\EventValue eav
          where eav.version = :version');
        $query->setParameter('version', $version);

        return $query->getResult();
    }

    /**
     * @param Key $key
     * @param \Concrete\Core\Entity\Calendar\CalendarEventVersion $version
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
