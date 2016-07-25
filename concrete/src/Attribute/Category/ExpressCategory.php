<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Controller\SinglePage\Dashboard\Express;
use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Attribute\ExpressSetManager;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Entity\Express\Entity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class ExpressCategory extends AbstractCategory
{

    protected $expressEntity;

    public function getIndexedSearchTable()
    {
        return camelcase($this->expressEntity->getHandle())
            . 'ExpressSearchIndexAttributes';
    }


    public function getSearchIndexer()
    {
        $indexer = $this->application->make('Concrete\Core\Attribute\Category\SearchIndexer\ExpressSearchIndexer');

        return $indexer;
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

    public function getSetManager()
    {
        if (!isset($this->setManager)) {
            $this->setManager = new ExpressSetManager($this->expressEntity, $this->entityManager);
        }
        return $this->setManager;
    }

    public function __construct(Entity $entity, Application $application, EntityManager $entityManager)
    {
        $this->expressEntity = $entity;
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

    public function allowAttributeSets()
    {
        return false;
    }

    public function getList()
    {
        return $this->getAttributeRepository()->findBy(array('entity' => $this->expressEntity));
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
        $key->setEntity($this->expressEntity);
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
