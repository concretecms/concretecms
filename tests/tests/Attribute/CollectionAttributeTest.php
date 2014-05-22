<?php
use Concrete\Core\Attribute\Key\Category;

class CollectionAttributeTest extends AttributeTestCase {
	
	protected $fixtures = array();
    protected $keys = array(
        'exclude_nav' => array('akName' => 'Exclude from Nav', 'type' => 'boolean'),
        'exclude_page_list' => array('akName' => 'Exclude from Page List', 'type' => 'boolean'),
        'exclude_search_index' => array('akName' => 'Exclude from Search Index', 'type' => 'boolean'),
        'exclude_sitemapxml' => array('akName' => 'Exclude from Sitemap XML', 'type' => 'boolean'),
        'header_extra_content' => array('akName' => 'Header Extra Content', 'type' => 'textarea'),
        'meta_keywords' => array('akName' => 'Header Extra Content', 'type' => 'text'),
        'meta_description' => array('akName' => 'Header Extra Content', 'type' => 'text'),
        'meta_title' => array('akName' => 'Meta Title', 'type' => 'text')
    );

    protected function getAttributeKeyClass()
    {
        return '\Concrete\Core\Attribute\Key\CollectionKey';
    }

    protected function getAttributeObjectForGet()
    {
        $c = Page::getbyID($this->object->getCollectionID(), 'RECENT');
        return $c;
    }

    protected function setUp() {
        $this->tables = array_merge($this->tables, array(
            'Collections',
            'CollectionAttributeValues',
            'Pages',
            'CollectionSearchIndexAttributes',
            'CollectionVersions')
        );
        Category::add('collection');
        $this->object = Page::addHomePage();
        parent::setUp();
    }

    public function attributeValues() {
        return array(
            array('exclude_nav',
                true,
                false,
                '1',
                '0'
            ),
            array('exclude_page_list',
                true,
                false,
                '1',
                '0'
            ),
            array('exclude_search_index',
                true,
                false,
                '1',
                '0'
            ),
            array('exclude_sitemapxml',
                true,
                false,
                '1',
                '0'
            ),
            array('header_extra_content',
                '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js"></script>',
                '<script src="fake.js"></script>'
            ),
            array('meta_keywords',
                'trout, salmon, cod, sturgeon, flying fish',
                'horses, pigs, ducks'
            ),
            array('meta_description',
                'A great page about fish',
                'A fun page about farms'
            ),
            array('meta_title',
                'Fun Page',
                'Great Page'
            )
        );
    }

    public function attributeHandles() {
        return array(
            array('exclude_nav'),
            array('exclude_page_list'),
            array('exclude_search_index'),
            array('exclude_sitemapxml'),
            array('header_extra_content'),
            array('meta_keywords'),
            array('meta_description'),
            array('meta_title')
        );
    }

}