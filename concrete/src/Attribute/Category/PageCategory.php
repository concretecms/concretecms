<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\PageKey;

class PageCategory extends AbstractStandardCategory
{

    public function createAttributeKey()
    {
        return new PageKey();
    }

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
                    'options' => array('unsigned' => true, 'default' => 0, 'notnull' => true),
                ),
            ),
            'primary' => array('cID'),
        );
    }

    public function getAttributeKeyRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\PageKey');
    }

    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\PageValue');
    }


    public function getAttributeValues($page)
    {
        $values = $this->getAttributeValueRepository()->findBy(array(
            'cID' => $page->getCollectionID(),
            'cvID' => $page->getVersionID(),
        ));

        return $values;
    }

    public function getAttributeValue(Key $key, $page)
    {
        $value = $this->getAttributeValueRepository()->findOneBy(array(
            'cID' => $page->getCollectionID(),
            'cvID' => $page->getVersionID(),
            'attribute_key' => $key,
        ));

        return $value;
    }

}
