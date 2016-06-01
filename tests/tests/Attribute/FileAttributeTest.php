<?php

use Concrete\Core\Attribute\Key\Category;

class FileAttributeTest extends \AttributeTestCase
{
    protected $fixtures = array();
    protected $category;
    protected $keys = array(
        'width' => array('akName' => 'Width', 'type' => 'number'),
        'height' => array('akName' => 'Height', 'type' => 'number'),
    );

    protected $indexQuery = 'select * from FileSearchIndexAttributes where fID = 1';

    protected function getAttributeKeyClass()
    {
        return '\Concrete\Core\Attribute\Key\FileKey';
    }

    protected function getAttributeObjectForGet()
    {
        return $this->object;
    }

    protected function setUp()
    {
        $this->tables = array_merge($this->tables, array(
                'FileStorageLocationTypes',
                'FileVersionLog',
            )
        );
        $this->metadatas = array_merge($this->metadatas, array(
            'Concrete\Core\Entity\File\Version',
            'Concrete\Core\Entity\File\File',
            'Concrete\Core\Entity\Attribute\Key\Type\NumberType',
            'Concrete\Core\Entity\Attribute\Key\FileKey',
            'Concrete\Core\Entity\Attribute\Key\Key',
            'Concrete\Core\Entity\Attribute\Value\Value\NumberValue',
            'Concrete\Core\Entity\Attribute\Value\FileValue',
            'Concrete\Core\Entity\File\StorageLocation\StorageLocation',
        ));
        parent::setUp();
    }

    protected function installAttributeCategoryAndObject()
    {
        $type = \Concrete\Core\File\StorageLocation\Type\Type::add('default', t('Default'));
        $configuration = $type->getConfigurationObject();
        $fsl = \Concrete\Core\File\StorageLocation\StorageLocation::add($configuration, 'Default', true);

        $this->category = Category::add('file');
        $em = \Database::connection()->getEntityManager();

        $file = new \Concrete\Core\Entity\File\File();
        $file->setDateAdded(new \DateTime());
        $file->setStorageLocation($fsl);
        $em->persist($file);

        $em->flush();

        $version = new \Concrete\Core\Entity\File\Version();
        $version->setFile($file);
        $version->setFilename('test.jpg');
        $em->persist($version);

        $em->flush();

        $version->approve();

        $em->flush();
        $this->object = $file;
    }

    public function attributeValues()
    {
        return array(
            array(
                'width',
                200,
                0,
                200,
                0,
            ),
            array(
                'height',
                500,
                0,
                500,
                0,
            ),
        );
    }

    public function attributeIndexTableValues()
    {
        return array(
            array(
                'width',
                200,
                array(
                    'ak_width' => 200.0000,
                ),
            ),
        );
    }

    public function attributeHandles()
    {
        return array(
            array('width'),
            array('height'),
        );
    }
}
