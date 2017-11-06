<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Entity\Attribute\Key\FileKey;
use Concrete\Core\Entity\Attribute\Key\Key;

class FileCategory extends AbstractStandardCategory
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
        return [
            'columns' => [
                [
                    'name' => 'fID',
                    'type' => 'integer',
                    'options' => ['unsigned' => true, 'default' => 0, 'notnull' => true],
                ],
            ],
            'primary' => ['fID'],
        ];
    }

    public function getAttributeKeyRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\FileKey');
    }

    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\FileValue');
    }

    public function getAttributeValues($version)
    {
        $query = $this->entityManager->createQuery('select fav from Concrete\Core\Entity\Attribute\Value\FileValue fav
          where fav.fvID = :fvID and fav.fID = :fID');
        $query->setParameter('fID', $version->getFile()->getFileID());
        $query->setParameter('fvID', $version->getFileVersionID());

        return $query->getResult();
    }

    public function getAttributeValue(Key $key, $file)
    {
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\FileValue');
        $value = $r->findOneBy([
            'fID' => $file->getFileID(),
            'fvID' => $file->getFileVersionID(),
            'attribute_key' => $key,
        ]);

        return $value;
    }
}
