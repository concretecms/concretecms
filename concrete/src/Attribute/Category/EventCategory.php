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
        return array(
            'columns' => array(
                array(
                    'name' => 'eventID',
                    'type' => 'integer',
                    'options' => array('unsigned' => true, 'default' => 0, 'notnull' => true), ),
            ),
            'primary' => array('eventID'),
        );
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

    public function getAttributeValue(Key $key, $version)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\EventValue');
        $value = $r->findOneBy(array(
            'version' => $version,
            'attribute_key' => $key,
        ));

        return $value;
    }

}
