<?php

namespace Concrete\Tests\Attribute;

use Concrete\Core\Attribute\Key\Category;
use Concrete\TestHelpers\Attribute\AttributeTestCase;

class FileAttributeTest extends AttributeTestCase
{
    protected $fixtures = [];
    protected $category;
    protected $keys = [
        'width' => ['akName' => 'Width', 'type' => 'number'],
        'height' => ['akName' => 'Height', 'type' => 'number'],
    ];

    protected $indexQuery = 'select * from FileSearchIndexAttributes where fID = 1';

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables = array_merge($this->tables, [
                'FileStorageLocationTypes',
                'FileVersionLog',
            ]
        );
        $this->metadatas = array_merge($this->metadatas, [
            'Concrete\Core\Entity\File\Version',
            'Concrete\Core\Entity\File\File',
            'Concrete\Core\Entity\Attribute\Key\Settings\NumberSettings',
            'Concrete\Core\Entity\Attribute\Key\FileKey',
            'Concrete\Core\Entity\Attribute\Key\Key',
            'Concrete\Core\Entity\Attribute\Value\Value\NumberValue',
            'Concrete\Core\Entity\Attribute\Value\FileValue',
            'Concrete\Core\Entity\File\StorageLocation\Type\Type',
            'Concrete\Core\Entity\File\StorageLocation\StorageLocation',
        ]);
    }

    public function attributeValues()
    {
        return [
            [
                'width',
                '200',
                '0',
                '200',
                '0',
            ],
            [
                'height',
                '500',
                '0',
                '500',
                '0',
            ],
        ];
    }

    public function attributeIndexTableValues()
    {
        return [
            [
                'width',
                (float) 200,
                [
                    'ak_width' => 200.0000,
                ],
            ],
        ];
    }

    public function attributeHandles()
    {
        return [
            ['width'],
            ['height'],
        ];
    }

    protected function getAttributeKeyClass()
    {
        return '\Concrete\Core\Attribute\Key\FileKey';
    }

    protected function getAttributeObjectForGet()
    {
        return $this->object;
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
}
