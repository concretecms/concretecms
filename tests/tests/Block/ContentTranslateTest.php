<?php

namespace Concrete\Tests\Block;

use Concrete\Core\Editor\LinkAbstractor;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;

class ContentTranslateTest extends ConcreteDatabaseTestCase
{
    protected $fixtures = [];
    protected $tables = [
        'SystemContentEditorSnippets',
    ];

    protected $metadatas = [
        'Concrete\Core\Entity\File\File',
    ];

    public function setUp(): void
    {
        \Core::forgetInstance('url/canonical');

        parent::setUp();
    }

    /**
     * This is saving data from the content editor HTML INTO the database.
     *
     *  @dataProvider contentsTo
     *
     * @param mixed $from
     * @param mixed $to
     */
    public function testTo($from, $to)
    {
        $translated = LinkAbstractor::translateTo($from);
        $this->assertEquals($to, $translated);
    }

    /**
     * This is taking data OUT of the database and sending it into the content editor.
     *
     * @dataProvider contentsFromEditMode
     *
     * @param mixed $to
     * @param mixed $from
     */
    public function testFromEditMode($to, $from)
    {
        $translated = LinkAbstractor::translateFromEditMode($from);
        $this->assertEquals($to, $translated);
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
        $translated = LinkAbstractor::translateFrom($from);
        $this->assertEquals($to, $translated);
    }

    public static function contentsTo()
    {
        return [
           ['Simple', 'Simple'],
           ['<p><a href="http://www.dummyco.com/path/to/server/index.php?cID=50">Test</a></p>', '<p><a href="{CCM:CID_50}">Test</a></p>'],
           ['<p><a href="http://www.dummyco.com/path/to/server/">Test</a></p>', '<p><a href="{CCM:BASE_URL}/">Test</a></p>'],
           ['Test<img src="http://www.dummyco.com/path/to/server/index.php/download_file/view_inline/1">', 'Test<concrete-picture fID="1" />'],
           ['Test<img src="http://www.dummyco.com/path/to/server/index.php/download_file/view_inline/1" alt="Woohoo" style="display: block" />', 'Test<concrete-picture fID="1" alt="Woohoo" style="display: block" />'],
           ['<a href="http://www.dummyco.com/path/to/server/index.php/download_file/view/1">Test</a>', '<a href="{CCM:FID_DL_1}">Test</a>'],
           ['<a href="http://www.dummyco.com/path/to/server/index.php/download_file/view/5071af6f-13ec-4e88-b96b-2a2258996c4f">UUID Test</a>', '<a href="{CCM:FID_DL_5071af6f-13ec-4e88-b96b-2a2258996c4f}">UUID Test</a>'],
            ['<a href="http://www.dummyco.com/path/to/server/index.php/download_file/view/5071af6f-13ec-4e88-b96b-2a2258996c4f">http://www.dummyco.com/path/to/server/index.php/download_file/view/5071af6f-13ec-4e88-b96b-2a2258996c4f</a>', '<a href="{CCM:FID_DL_5071af6f-13ec-4e88-b96b-2a2258996c4f}">{CCM:FID_DL_5071af6f-13ec-4e88-b96b-2a2258996c4f}</a>'],
            ['<a href="http://www.dummyco.com/path/to/server/index.php/download_file/view/a7b03327-6de7-4beb-ba20-2172341bba6d">UUID Test 2</a>', '<a href="{CCM:FID_DL_a7b03327-6de7-4beb-ba20-2172341bba6d}">UUID Test 2</a>'],
        ];
    }

    public static function contentsFromEditMode()
    {
        return [
            ['Simple', 'Simple'],
            ['<p><a href="http://www.dummyco.com/path/to/server/index.php?cID=50">Test</a></p>', '<p><a href="{CCM:CID_50}">Test</a></p>'],
            ['Test<img src="http://www.dummyco.com/path/to/server/index.php/download_file/view_inline/1" alt="Woohoo" style="display: block" />', 'Test<concrete-picture fID="1" alt="Woohoo" style="display: block" />'],
            ['<a href="http://www.dummyco.com/path/to/server/index.php/download_file/view/1">Test</a>', '<a href="{CCM:FID_DL_1}">Test</a>'],
            ['<a href="http://www.dummyco.com/path/to/server/index.php/download_file/view/5071af6f-13ec-4e88-b96b-2a2258996c4f">Test</a>', '<a href="{CCM:FID_DL_5071af6f-13ec-4e88-b96b-2a2258996c4f}">Test</a>'],
            ['<a href="http://www.dummyco.com/path/to/server/index.php/download_file/view/a7b03327-6de7-4beb-ba20-2172341bba6d">Test</a>', '<a href="{CCM:FID_DL_a7b03327-6de7-4beb-ba20-2172341bba6d}">Test</a>'],
            ['<p><a href="http://www.dummyco.com/path/to/server">Test</a></p>', '<p><a href="{CCM:BASE_URL}">Test</a></p>'],
        ];
    }

    public static function contentsFrom()
    {
        return [
            ['Simple', 'Simple'],
            ['<p>Super super super {CCM:BASE_URL} !!</p>', '<p>Super super super http://www.dummyco.com/path/to/server !!</p>'],
            ['<p><a href="{CCM:FID_DL_8}">Download File</a></p>', '<p><a href="http://www.dummyco.com/path/to/server/index.php/download_file/view/8">Download File</a></p>'],
            ['<p><a href="{CCM:FID_DL_5071af6f-13ec-4e88-b96b-2a2258996c4f}">Download File</a></p>', '<p><a href="http://www.dummyco.com/path/to/server/index.php/download_file/view/5071af6f-13ec-4e88-b96b-2a2258996c4f">Download File</a></p>'],
            ['<p><a href="{CCM:FID_DL_a7b03327-6de7-4beb-ba20-2172341bba6d}">Download File</a></p>', '<p><a href="http://www.dummyco.com/path/to/server/index.php/download_file/view/a7b03327-6de7-4beb-ba20-2172341bba6d">Download File</a></p>'],
        ];
    }
}
