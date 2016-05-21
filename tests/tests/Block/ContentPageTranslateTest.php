<?php

class ContentPageTranslateTest extends PageTestCase
{
    protected $fixtures = array();

    protected function setUp()
    {
        $this->tables = array_merge($this->tables, array(
            'SystemContentEditorSnippets',
        ));
        \Core::forgetInstance('url/canonical');
        parent::setUp();
    }

    /**
     * This is taking data OUT of the database and sending it into the page.
     *
     *  @dataProvider contentsFrom
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

        return array(
            array('<a href="{CCM:CID_3}">Super Cool!</a>',
                '<a href="' . \Core::getApplicationURL() . '/' . DISPATCHER_FILENAME . '/awesome/all-right">Super Cool!</a>',
            ),
        );
    }
}
