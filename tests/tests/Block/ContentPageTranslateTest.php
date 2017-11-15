<?php

namespace Concrete\Tests\Block;

use Concrete\TestHelpers\Page\PageTestCase;

class ContentPageTranslateTest extends PageTestCase
{
    protected $fixtures = [];

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tables = array_merge($this->tables, [
            'SystemContentEditorSnippets',
        ]);
    }

    public function setUp()
    {
        \Core::forgetInstance('url/canonical');
        parent::setUp();
    }

    /**
     * This is taking data OUT of the database and sending it into the page.
     *
     *  @dataProvider contentsFrom
     *
     * @param mixed $from
     * @param mixed $to
     */
    public function testFrom($from, $to)
    {
        self::createPage('Awesome');
        self::createPage('All Right', '/awesome');
        $translated = \Concrete\Core\Editor\LinkAbstractor::translateFrom($from);
        $this->assertEquals($to, $translated);
    }

    public function contentsFrom()
    {
        \Core::forgetInstance('url/canonical');

        return [
            ['<a href="{CCM:CID_3}">Super Cool!</a>',
                '<a href="' . \Core::getApplicationURL() . '/' . DISPATCHER_FILENAME . '/awesome/all-right">Super Cool!</a>',
            ],
        ];
    }
}
