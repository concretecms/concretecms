<?php


namespace Concrete\Tests\Page\Search;


use Concrete\Core\Page\Search\IndexedSearch;
use PHPUnit\Framework\TestCase;

class IndexedSearchTest extends TestCase
{

    public function testStripBadTagContents()
    {
        $testString = <<<STR
1
<script>
// Normal script tag
foo
</script>
2
<SCRIPT CAPITALIZED="YA">
foo
</SCRIPT>
3
<script >
foo
</script   >
4
<script type="text/javascript" >
foo
</script>
5
STR;

        $indexedSearch = new IndexedSearch();

        $method = new \ReflectionMethod($indexedSearch, 'stripBadTagContents');
        $method->setAccessible(true);

        $expected = implode("\n\n", range(1,5));
        $this->assertEquals($expected, $method->invoke($indexedSearch, $testString));
    }

}
