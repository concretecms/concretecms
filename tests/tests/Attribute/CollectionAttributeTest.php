<?php

namespace Concrete\Tests\Attribute;

use Concrete\Core\Attribute\Key\Category;
use Concrete\TestHelpers\Attribute\AttributeTestCase;
use Page;

class CollectionAttributeTest extends AttributeTestCase
{
    protected $fixtures = [];
    protected $category;
    protected $keys = [
        'exclude_nav' => ['akName' => 'Exclude from Nav', 'type' => 'boolean'],
        'exclude_page_list' => ['akName' => 'Exclude from Page List', 'type' => 'boolean'],
        'exclude_sitemapxml' => ['akName' => 'Exclude from Sitemap XML', 'type' => 'boolean'],
        'header_extra_content' => ['akName' => 'Header Extra Content', 'type' => 'textarea'],
        'meta_keywords' => ['akName' => 'Header Extra Content', 'type' => 'text'],
        'meta_description' => ['akName' => 'Header Extra Content', 'type' => 'text'],
        'meta_title' => ['akName' => 'Meta Title', 'type' => 'text'],
    ];

    protected $indexQuery = 'select * from CollectionSearchIndexAttributes where cID = 1';

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables = array_merge($this->tables, [
            'Collections',
            'CollectionAttributeValues',
            'Pages',
            'PageSearchIndex',
            'PageTypes',
            'CollectionSearchIndexAttributes',
            'CollectionVersions',
            'CollectionVersionBlocks',
            'GatheringDataSources', ]
        );
        $this->metadatas = array_merge($this->metadatas, [
            'Concrete\Core\Entity\Page\PagePath',
            'Concrete\Core\Entity\Page\Template',
        ]);
    }

    public function attributeValues()
    {
        return [
            ['exclude_nav',
                true,
                false,
                true,
                false,
            ],
            ['exclude_page_list',
                true,
                false,
                true,
                false,
            ],
            ['exclude_sitemapxml',
                true,
                false,
                true,
                false,
            ],
            ['header_extra_content',
                '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>',
                '<script src="fake.js"></script>',
            ],
            ['meta_keywords',
                'trout, salmon, cod, sturgeon, flying fish',
                'horses, pigs, ducks',
            ],
            ['meta_description',
                'A great page about fish',
                'A fun page about farms',
            ],
            ['meta_title',
                'Fun Page',
                'Great Page',
            ],
        ];
    }

    public function attributeIndexTableValues()
    {
        return [
            ['exclude_nav',
                true,
                [
                    'ak_exclude_nav' => '1',
                ],
            ],
            ['meta_title',
                'Fun Page',
                [
                    'ak_meta_title' => 'Fun Page',
                ],
            ],
        ];
    }

    public function attributeHandles()
    {
        return [
            ['exclude_nav'],
            ['exclude_page_list'],
            ['exclude_sitemapxml'],
            ['header_extra_content'],
            ['meta_keywords'],
            ['meta_description'],
            ['meta_title'],
        ];
    }

    protected function getAttributeKeyClass()
    {
        return '\Concrete\Core\Attribute\Key\CollectionKey';
    }

    protected function getAttributeObjectForGet()
    {
        $c = Page::getbyID($this->object->getCollectionID(), 'RECENT');

        return $c;
    }

    protected function installAttributeCategoryAndObject()
    {
        $this->category = Category::add('collection');
        $this->object = Page::addHomePage();
    }
}
