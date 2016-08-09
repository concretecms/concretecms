<?php
namespace Concrete\Tests\CodingStyle;

class NoShortTagsTest extends \Concrete\Tests\CodingStyleTestCase
{
    /**
     *  @dataProvider phpFilesProvider
     */
    public function testNoShortTags($phpFile)
    {
        $sot = ini_get('short_open_tag');
        if (empty($sot)) {
            static::markTestSkipped('short_open_tag must be enabled in order to run the test '.__CLASS__.'::'.__FUNCTION__);

            return;
        }
        $contents = @file_get_contents($phpFile);
        $this->assertNotFalse($contents, 'Failed to read file '.$phpFile);
        $tokens = @token_get_all($contents);
        $this->assertTrue(is_array($tokens), 'Failed to retrieve the PHP tokes of the file '.$phpFile);
        foreach ($tokens as $token) {
            if (is_array($token)) {
                switch ($token[0]) {
                    case T_OPEN_TAG:
                        $this->assertSame('<?php', trim($token[1]), "Short tag found in file $phpFile at line {$token[2]}");
                        break;
                }
            }
        }
    }
}
