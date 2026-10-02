<?php

namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Entity\Attribute\Key\FileKey;
use Concrete\Core\Entity\Attribute\Key\Key;

/**
 * The attribute keys of this category are \Concrete\Core\Entity\Attribute\Key\FileKey instances.
 *
 * @method \Concrete\Core\Entity\Attribute\Key\FileKey|null getAttributeKeyByID(int $akID)
 * @method \Concrete\Core\Entity\Attribute\Key\FileKey|null getAttributeKeyByHandle(string $akHandle)
 * @method \Concrete\Core\Entity\Attribute\Key\FileKey|null getByID(int $akID)
 * @method \Concrete\Core\Entity\Attribute\Key\FileKey|null getByHandle(string $akHandle)
 * @method \Concrete\Core\Entity\Attribute\Key\FileKey[] getList()
 * @method \Concrete\Core\Entity\Attribute\Key\FileKey[] getSearchableList()
 * @method \Concrete\Core\Entity\Attribute\Key\FileKey[] getSearchableIndexedList()
 * @method \Concrete\Core\Entity\Attribute\Key\FileKey addFromRequest(\Concrete\Core\Entity\Attribute\Type $type, \Symfony\Component\HttpFoundation\Request $request)
 * @method \Concrete\Core\Entity\Attribute\Key\FileKey import(\Concrete\Core\Entity\Attribute\Type $type, \SimpleXMLElement $element, ?\Concrete\Core\Entity\Package $package = null)
 * @method \Concrete\Core\Entity\Attribute\Key\FileKey updateFromRequest(\Concrete\Core\Entity\Attribute\Key\FileKey $key, \Symfony\Component\HttpFoundation\Request $request)
 */
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
        return $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\Key\FileKey');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\AbstractCategory::getAttributeValueRepository()
     */
    public function getAttributeValueRepository()
    {
        return $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\Value\FileValue');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\Category\CategoryInterface::getAttributeValues()
     *
     * @param \Concrete\Core\Entity\File\Version|\Concrete\Core\Entity\File\File $version the file version (or a file: in this case its approved version is used)
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
        $cacheKey = sprintf('attribute/value/%s/file/%d/%d', $key->getAttributeKeyHandle(), $file->getFileID(), $file->getFileVersionID());
        $parameters = [
            'fID' => $file->getFileID(),
            'fvID' => $file->getFileVersionID(),
            'attribute_key' => $key,
        ];

        return $this->getAttributeValueEntity($cacheKey, $parameters);
    }
}
