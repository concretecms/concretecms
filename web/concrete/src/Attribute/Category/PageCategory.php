<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\Category\SearchIndexer\PageSearchIndexer;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Type as AttributeType;
use Concrete\Core\Entity\Page\Attribute;
use Concrete\Core\Page\Page;
use Symfony\Component\HttpFoundation\Request;

class PageCategory extends AbstractCategory implements StandardSearchIndexerInterface
{

    public function getIndexedSearchTable()
    {
        return 'CollectionSearchIndexAttributes';
    }

    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return $mixed->getCollectionID();
    }

    public function getSearchIndexFieldDefinition()
    {
        return array(
            'columns' => array(
                array(
                    'name' => 'cID',
                    'type' => 'integer',
                    'options' => array('unsigned' => true, 'default' => 0, 'notnull' => true)
                )
            ),
            'primary' => array('cID')
        );
    }

    public function getAttributeRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Page\Attribute');
    }

    /**
     * Takes an attribute key as created by the subroutine and assigns it to the page category.
     * @param Key $key
     */
    protected function assignToCategory(Key $key)
    {
        $this->entityManager->persist($key);
        $this->entityManager->flush();
        $attribute = new Attribute();
        $attribute->setAttributeKey($key);
        $this->entityManager->persist($attribute);
        $this->entityManager->flush();
    }

    public function addFromRequest(AttributeType $type, Request $request)
    {
        $key = parent::addFromRequest($type, $request);
        $this->assignToCategory($key);
    }

    public function import(AttributeType $type, \SimpleXMLElement $element)
    {
        $key = parent::import($type, $element);
        $this->assignToCategory($key);
    }

    public function delete(Key $key)
    {
        $query = $this->entityManager->createQuery(
            'select a from Concrete\Core\Entity\Page\Attribute a where a.attribute_key = :key'
        );
        $query->setParameter('key', $key);
        $attribute = $query->getSingleResult();
        if (is_object($attribute)) {
            $this->entityManager->remove($attribute);
            $this->entityManager->flush();
        }
    }

    public function getAttributeValues($page)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Page\AttributeValue');
        $values = $r->findBy(array(
            'cID' => $page->getCollectionID(),
            'cvID' => $page->getVersionID()
        ));
        return $values;
    }

}
