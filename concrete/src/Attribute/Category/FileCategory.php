<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Entity\Attribute\Key\FileKey;
use Concrete\Core\Entity\Attribute\Key\Key;

class FileCategory extends AbstractStandardCategory
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::createAttributeKey()
     *
     * @return \Concrete\Core\Entity\Attribute\Key\FileKey
     */
    public function createAttributeKey()
    {
        return new FileKey();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface::getIndexedSearchTable()
     */
    public function getIndexedSearchTable()
    {
        return 'FileSearchIndexAttributes';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface::getIndexedSearchPrimaryKeyValue()
     *
     * @param \Concrete\Core\Entity\File\File $mixed
     *
     * @return int
     */
    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return $mixed->getFileID();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface::getSearchIndexFieldDefinition()
     */
    public function getSearchIndexFieldDefinition()
    {
        return [
            'columns' => [
                [
                    'name' => 'fID',
                    'type' => 'integer',
                    'options' => ['unsigned' => true, 'notnull' => true],
                ],
            ],
            'primary' => ['fID'],
            'foreignKeys' => [
                [
                    'foreignTable' => 'Files',
                    'localColumns' => ['fID'],
                    'foreignColumns' => ['fID'],
                    'onUpdate' => 'CASCADE',
                    'onDelete' => 'CASCADE',
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getAttributeKeyRepository()
     */
    public function getAttributeKeyRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Key\FileKey');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getAttributeValueRepository()
     */
    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\Value\FileValue');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getAttributeValues()
     *
     * @param \Concrete\Core\Entity\File\Version $version
     *
     * @return \Concrete\Core\Entity\Attribute\Value\FileValue[]
     */
    public function getAttributeValues($version)
    {
        $query = $this->entityManager->createQuery('select fav from Concrete\Core\Entity\Attribute\Value\FileValue fav
          where fav.fvID = :fvID and fav.fID = :fID');
        $query->setParameter('fID', $version->getFile()->getFileID());
        $query->setParameter('fvID', $version->getFileVersionID());

        return $query->getResult();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getAttributeValue()
     *
     * @param \Concrete\Core\Entity\Attribute\Key\FileKey $key
     * @param \Concrete\Core\Entity\File\Version $file
     *
     * @return \Concrete\Core\Entity\Attribute\Value\FileValue|null
     */
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
