<?php

namespace Concrete\Tests\Utility;

use Concrete\Core\Utility\SearchIndexContentSanitizer;
use Concrete\Tests\TestCase;

class SearchIndexContentSanitizerTest extends TestCase
{
    /**
     * @dataProvider provideSanitizedContent
     */
    public function testSanitize(string $input, string $expected): void
    {
        $sanitizer = new SearchIndexContentSanitizer();

        $this->assertSame($expected, $sanitizer->sanitize($input));
    }

    public static function provideSanitizedContent(): array
    {
        return [
            'script and style bodies are removed' => [
                '<p>Hello</p><style>.hero { color: red; }</style><script>alert("x");</script><div>World</div>',
                'Hello World',
            ],
            'entities and block boundaries are normalized' => [
                '<div>Fish&nbsp;&amp;&nbsp;Chips</div><p>and<br>Peas</p>',
                'Fish & Chips and Peas',
            ],
            'malformed markup still preserves visible text' => [
                '<div>One<p>Two</div><span>Three</span>',
                'One Two Three',
            ],
            'plain text is preserved' => [
                'Already plain text',
                'Already plain text',
            ],
        ];
    }
}
