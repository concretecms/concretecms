<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Entity\Attribute\Key\FileKey;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Type;
use Concrete\Core\Entity\File\Attribute;
use Symfony\Component\HttpFoundation\Request;

class FileCategory extends AbstractCategory implements StandardSearchIndexerInterface
{

    public function createAttributeKey()
    {
        return new FileKey();
    }

    public function getIndexedSearchTable()
    {
        return 'FileSearchIndexAttributes';
    }

    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return $mixed->getFileID();
    }


    public function getSearchIndexFieldDefinition()
    {
        return array(
            'columns' => array(
                array(
                    'name' => 'fID',
                    'type' => 'integer',
                    'options' => array('unsigned' => true, 'default' => 0, 'notnull' => true)
                )
            ),
            'primary' => array('fID')
        );
    }

    public function getAttributeRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\FileKey');
    }

    public function getAttributeValues($file)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\File\AttributeValue');
        $values = $r->findBy(array(
            'fID' => $file->getFileID(),
            'fvID' => $file->getVersionID()
        ));
        return $values;
    }



}
