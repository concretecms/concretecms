<?php

use Concrete\Core\Attribute\Key\Category;

class CollectionAttributeTest extends \AttributeTestCase
{
    protected $fixtures = array();
    protected $category;
    protected $keys = array(
        'exclude_nav' => array('akName' => 'Exclude from Nav', 'type' => 'boolean'),
        'exclude_page_list' => array('akName' => 'Exclude from Page List', 'type' => 'boolean'),
        'exclude_sitemapxml' => array('akName' => 'Exclude from Sitemap XML', 'type' => 'boolean'),
        'header_extra_content' => array('akName' => 'Header Extra Content', 'type' => 'textarea'),
        'meta_keywords' => array('akName' => 'Header Extra Content', 'type' => 'text'),
        'meta_description' => array('akName' => 'Header Extra Content', 'type' => 'text'),
        'meta_title' => array('akName' => 'Meta Title', 'type' => 'text'),
    );

    protected $indexQuery = 'select * from CollectionSearchIndexAttributes where cID = 1';

    protected function getAttributeKeyClass()
    {
        return '\Concrete\Core\Attribute\Key\CollectionKey';
    }

    protected function getAttributeObjectForGet()
    {
        $c = Page::getbyID($this->object->getCollectionID(), 'RECENT');

        return $c;
    }

    protected function setUp()
    {
        $this->tables = array_merge($this->tables, array(
            'Collections',
            'CollectionAttributeValues',
            'Pages',
            'PagePaths',
            'PageSearchIndex',
            'CollectionSearchIndexAttributes',
            'CollectionVersions',
            'CollectionVersionBlocks',
            'GatheringDataSources', )
        );
        parent::setUp();
    }

    protected function installAttributeCategoryAndObject()
    {
        $this->category = Category::add('collection');
        $this->object = Page::addHomePage();
    }

    public function attributeValues()
    {
        return array(
            array('exclude_nav',
                true,
                false,
                '1',
                '0',
            ),
            array('exclude_page_list',
                true,
                false,
                '1',
                '0',
            ),
            array('exclude_sitemapxml',
                true,
                false,
                '1',
                '0',
            ),
            array('header_extra_content',
                '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>',
                '<script src="fake.js"></script>',
            ),
            array('meta_keywords',
                'trout, salmon, cod, sturgeon, flying fish',
                'horses, pigs, ducks',
            ),
            array('meta_description',
                'A great page about fish',
                'A fun page about farms',
            ),
            array('meta_title',
                'Fun Page',
                'Great Page',
            ),
        );
    }

    public function attributeIndexTableValues()
    {
        return array(
            array('exclude_nav',
                true,
                array(
                    'ak_exclude_nav' => '1',
                ),
            ),
            array('meta_title',
                'Fun Page',
                array(
                    'ak_meta_title' => 'Fun Page',
                ),
            ),
        );
    }

    public function attributeHandles()
    {
        return array(
            array('exclude_nav'),
            array('exclude_page_list'),
            array('exclude_sitemapxml'),
            array('header_extra_content'),
            array('meta_keywords'),
            array('meta_description'),
            array('meta_title'),
        );
    }
}
