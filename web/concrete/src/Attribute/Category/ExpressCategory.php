<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Controller\SinglePage\Dashboard\Express;
use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Entity\Express\Entity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class ExpressCategory extends AbstractCategory implements StandardSearchIndexerInterface
{

    public function getIndexedSearchTable()
    {
        return camelcase($this->entity->getHandle())
            . 'ExpressSearchIndexAttributes';
    }

    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return $mixed->getID();
    }

    public function getSearchIndexFieldDefinition()
    {
        return array(
            'columns' => array(
                array(
                    'name' => 'exEntryID',
                    'type' => 'integer',
                    'options' => array('unsigned' => true, 'default' => 0, 'notnull' => true),
                ),
            ),
            'primary' => array('exEntryID'),
        );
    }

    public function __construct(Entity $entity, Application $application, EntityManager $entityManager)
    {
        $this->setEntity($entity);
        parent::__construct($application, $entityManager);
    }

    public function createAttributeKey()
    {
        return new ExpressKey();
    }

    public function getAttributeRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\ExpressKey');
    }

    public function getAttributeSets()
    {
        return array();
    }

    public function allowAttributeSets()
    {
        return false;
    }

    public function getList()
    {
        return $this->getAttributeRepository()->findBy(array('entity' => $this->getEntity()));
    }

    public function getUnassignedAttributeKeys()
    {
        return $this->getList();
    }

    public function getAttributeKeyByHandle($handle)
    {
        return $this->getAttributeRepository()->findOneBy(array(
            'akHandle' => $handle,
            'entity' => $this->getEntity(),
        ));
    }

    public function getAttributeTypes()
    {
        return $this->entityManager
            ->getRepository('\Concrete\Core\Entity\Attribute\Type')
            ->findAll();
    }


    public function addFromRequest(Type $type, Request $request)
    {
        /**
         * @var $key ExpressKey
         */
        $key = parent::addFromRequest($type, $request);
        $key->setEntity($this->getEntity());
        return $key;
    }

    public function getAttributeValues($entry)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\ExpressValue');
        $values = $r->findBy(array(
            'entry' => $entry,
        ));

        return $values;
    }

    public function getAttributeValue(Key $key, $entry)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\ExpressValue');
        $value = $r->findOneBy(array(
            'entry' => $entry,
            'attribute_key' => $key,
        ));

        return $value;
    }
}
