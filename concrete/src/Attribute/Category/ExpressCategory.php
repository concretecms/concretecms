<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\ExpressSetManager;
use Concrete\Core\Entity\Attribute\Key\ExpressKey;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Package;
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

    public function getExpressEntity()
    {
        return $this->expressEntity;
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

    public function getSearchableIndexedList()
    {
        return $this->getAttributeKeyRepository()->findBy([
            'entity' => $this->expressEntity,
            'akIsSearchableIndexed' => true
        ]);
    }

    public function getSearchableList()
    {
        return $this->getAttributeKeyRepository()->findBy([
            'entity' => $this->expressEntity,
            'akIsSearchable' => true
        ]);
    }

    public function getSearchIndexFieldDefinition()
    {
        return [
            'columns' => [
                [
                    'name' => 'exEntryID',
                    'type' => 'integer',
                    'options' => ['unsigned' => true, 'default' => 0, 'notnull' => true],
                ],
            ],
            'primary' => ['exEntryID'],
        ];
    }

    public function deleteKey(Key $key)
    {
        $controls = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Control\AttributeKeyControl')
            ->findBy(['attribute_key' => $key]);
        foreach($controls as $control) {
            $this->entityManager->remove($control);
        }
        $this->entityManager->flush();

        parent::deleteKey($key);
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

    public function getAttributeKeyRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\ExpressKey');
    }

    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\ExpressValue');
    }

    public function allowAttributeSets()
    {
        return false;
    }

    public function getList()
    {
        return $this->getAttributeKeyRepository()->findBy(['entity' => $this->expressEntity]);
    }

    public function getAttributeTypes()
    {
        return $this->entityManager
            ->getRepository('\Concrete\Core\Entity\Attribute\Type')
            ->findAll();
    }

    public function import(Type $type, \SimpleXMLElement $element, Package $package = null)
    {
        $key = parent::import($type, $element, $package);
        $key->setEntity($this->expressEntity);

        return $key;
    }

    public function addFromRequest(Type $type, Request $request)
    {
        $key = parent::addFromRequest($type, $request);
        /*
         * @var ExpressKey
         */
        $key->setEntity($this->expressEntity);

        return $key;
    }

    public function getAttributeValues($entry)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\ExpressValue');
        $values = $r->findBy([
            'entry' => $entry,
        ]);

        return $values;
    }

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
