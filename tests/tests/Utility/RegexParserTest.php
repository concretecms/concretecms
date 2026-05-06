<?php

declare(strict_types=1);

namespace Concrete\Tests\Utility;

use Concrete\Core\Utility\RegexParser;
use Concrete\Core\Utility\RegexParser\ParsedRegex;
use Concrete\Tests\TestCase;

class RegexParserTest extends TestCase
{
    /**
     * @var \Concrete\Core\Utility\RegexParser
     */
    private static $parser;

    /**
     * @var array<string, \ReflectionMethod>
     */
    private static $parserPrivateMethods;

    public static function setUpBeforeClass(): void
    {
        self::$parser = new RegexParser();
        self::$parserPrivateMethods = [];
        $class = new \ReflectionClass(self::$parser);
        foreach ($class->getMethods(\ReflectionMethod::IS_PROTECTED | \ReflectionMethod::IS_PRIVATE) as $method) {
            if (PHP_VERSION < 80500) {
                $method->setAccessible(true);
            }
            self::$parserPrivateMethods[$method->name] = $method;
        }
    }

    /**
     * @dataProvider splitRegexProvider
     */
    public function testSplitRegex(string $regex, ?array $expected = null): void
    {
        $actual = self::$parserPrivateMethods['splitRegex']->invoke(self::$parser, $regex);
        self::assertSame($expected, $actual);
    }

    public static function splitRegexProvider(): array
    {
        return [
            [''],
            ['^$'],
            ['a.a'],
            ['<.>#'],
            ['//', ['delimiter' => '/', 'pattern' => '', 'modifiers' => '']],
            ['//i', ['delimiter' => '/', 'pattern' => '', 'modifiers' => 'i']],
            ['//imsxADUXJu', ['delimiter' => '/', 'pattern' => '', 'modifiers' => 'imsxADUXJu']],
            ['#.#', ['delimiter' => '#', 'pattern' => '.', 'modifiers' => '']],
            ['[.]', ['delimiter' => '[', 'pattern' => '.', 'modifiers' => '']],
            ['{.}', ['delimiter' => '{', 'pattern' => '.', 'modifiers' => '']],
            ['(.)', ['delimiter' => '(', 'pattern' => '.', 'modifiers' => '']],
        ];
    }

    /**
     * @dataProvider patternStartsWithCaretProvider
     */
    public function testPatternStartsWithCaret(string $pattern, bool $expected): void
    {
        $actual = self::$parserPrivateMethods['patternStartsWithCaret']->invoke(self::$parser, $pattern);

        self::assertSame($expected, $actual);
    }

    public static function patternStartsWithCaretProvider(): array
    {
        return [
            ['', false],
            ['$', false],
            ['^', true],
            ['^.', true],
        ];
    }

    /**
     * @dataProvider patternEndsWithDollarProvider
     */
    public function testPatternEndsWithDollar(string $pattern, bool $expected): void
    {
        $actual = self::$parserPrivateMethods['patternEndsWithDollar']->invoke(self::$parser, $pattern);

        self::assertSame($expected, $actual);
    }

    public static function patternEndsWithDollarProvider(): \Generator
    {
        yield ['', false];
        yield ['^', false];
        for ($numBackSlashes = 0; $numBackSlashes < 6; $numBackSlashes++) {
            $isEscaped = ($numBackSlashes % 2) === 1;
            $trailing = str_repeat('\\', $numBackSlashes) . '$';
            yield [$trailing, $isEscaped ? false : true];
            yield [".{$trailing}", $isEscaped ? false : true];
        }
    }

    /**
     * @dataProvider isConvertibleToTextSearchProvider
     */
    public function testIsConvertibleToTextSearch(string $pattern, string $delimiter, bool $expected): void
    {
        $actual = self::$parserPrivateMethods['isConvertibleToTextSearch']->invoke(self::$parser, $pattern, $delimiter);

        self::assertSame($expected, $actual);
    }

    public static function isConvertibleToTextSearchProvider(): \Generator
    {
        foreach (self::listConvertibleToText() as $key => [$pattern, $delimiter, $convertedToText]) {
            yield $key => [$pattern, $delimiter, $convertedToText !== null];
        }
    }

    /**
     * @dataProvider unescapeSimplePatternProvider
     */
    public function testUnescapeSimplePattern(string $pattern, string $delimiter, string $expected): void
    {
        $actual = self::$parserPrivateMethods['unescapeSimplePattern']->invoke(self::$parser, $pattern, $delimiter);

        self::assertSame($expected, $actual);
    }

    public static function unescapeSimplePatternProvider(): \Generator
    {
        foreach (self::listConvertibleToText() as $key => [$pattern, $delimiter, $convertedToText]) {
            if ($convertedToText !== null) {
                yield $key => [$pattern, $delimiter, $convertedToText];
            }
        }
    }

    private static function listConvertibleToText(): \Generator
    {
        // base cases
        yield 'empty string' => ['', '/', ''];
        yield 'simple text' => ['foo', '/', 'foo'];

        // metacharacters (non escaped)
        yield 'dot not escaped' => ['foo.', '/', null];
        yield 'dot in middle' => ['foo.bar', '/', null];
        yield 'plus' => ['foo+bar', '/', null];
        yield 'star' => ['foo*bar', '/', null];
        yield 'question mark' => ['foo?bar', '/', null];
        yield 'pipe' => ['foo|bar', '/', null];
        yield 'grouping' => ['foo(bar)', '/', null];
        yield 'brackets' => ['foo[bar]', '/', null];
        yield 'braces' => ['foo{1,2}', '/', null];

        // escaped literals
        yield 'escaped dot' => ['foo\\.bar', '/', 'foo.bar'];
        yield 'escaped slash' => ['foo\\/bar', '/', 'foo/bar'];
        yield 'escaped plus' => ['foo\\+bar', '/', 'foo+bar'];
        yield 'escaped star' => ['foo\\*bar', '/', 'foo*bar'];
        yield 'escaped question' => ['foo\\?bar', '/', 'foo?bar'];
        yield 'escaped pipe' => ['foo\\|bar', '/', 'foo|bar'];
        yield 'escaped bracket open' => ['foo\\[bar', '/', 'foo[bar'];
        yield 'escaped bracket close' => ['foo\\]bar', '/', 'foo]bar'];

        // anchors
        yield 'caret at start invalid' => ['^foo', '/', null];
        yield 'dollar at end invalid' => ['foo$', '/', null];
        yield 'both anchors' => ['^foo$', '/', null];

        // special classes (always regex)
        yield 'digit class' => ['foo\\dbar', '/', null];
        yield 'word class' => ['foo\\wbar', '/', null];
        yield 'space class' => ['foo\\sbar', '/', null];
        yield 'digit class uppercase' => ['foo\\Dbar', '/', null];

        // delimiter handling
        yield 'escaped delimiter / allowed' => ['foo\\/bar', '/', 'foo/bar'];
        yield 'escaped delimiter # allowed' => ['foo\\#bar', '#', 'foo#bar'];

        // mixed cases
        yield 'mixed escaped and literal dot' => ['foo\\.bar.baz', '/', null];
        yield 'multiple escaped literals only' => ['a\\.b\\+c\\\\_d', '/', 'a.b+c\\_d'];

        // edge case
        yield 'trailing backslash invalid' => ['foo\\', '/', null];
    }

    /**
     * @dataProvider parseRegexProvider
     */
    public function testParseRegex(string $regex, string $expectedType, string $expectedText, string $expectedDelimiter, string $expectedModifiers, string $normalizedRegex = ''): void
    {
        if ($normalizedRegex === '') {
            $normalizedRegex = $regex;
        }
        $result = self::$parser->parseRegEx($regex);

        self::assertSame($expectedType, $result->getType());
        self::assertSame($expectedText, $result->getText());
        self::assertSame($expectedDelimiter, $result->getDelimiter());
        self::assertSame($expectedModifiers, $result->getModifiers());
        self::assertSame($normalizedRegex, $result->asRegex());
    }

    public static function parseRegExProvider(): \Generator
    {
        yield 'simple regex with dot' => [
            '/foo.bar/',
            ParsedRegex::TYPE_REGEX,
            'foo.bar',
            '/',
            '',
        ];

        yield 'regex with caseless modifier' => [
            '/foo.bar/i',
            ParsedRegex::TYPE_REGEX,
            'foo.bar',
            '/',
            'i',
        ];

        yield 'equals type - both anchors' => [
            '/^exact match$/i',
            ParsedRegex::TYPE_EQUALS,
            'exact match',
            '/',
            'i',
        ];

        yield 'startsWith type - caret anchor' => [
            '/^starts with/',
            ParsedRegex::TYPE_STARTSWITH,
            'starts with',
            '/',
            '',
        ];

        yield 'endsWith type - dollar anchor' => [
            '/ends with$/',
            ParsedRegex::TYPE_ENDSWITH,
            'ends with',
            '/',
            '',
        ];

        yield 'contains type - no anchors' => [
            '/contains text/',
            ParsedRegex::TYPE_CONTAINS,
            'contains text',
            '/',
            '',
        ];

        yield 'hash delimiter equals' => [
            '#^exact#',
            ParsedRegex::TYPE_STARTSWITH,
            'exact',
            '#',
            '',
        ];

        yield 'bracket delimiter' => [
            '[^test$]',
            ParsedRegex::TYPE_EQUALS,
            'test',
            '[',
            '',
        ];

        yield 'brace delimiter' => [
            '{pattern}m',
            ParsedRegex::TYPE_CONTAINS,
            'pattern',
            '{',
            'm',
        ];

        yield 'parenthesis delimiter' => [
            '(^text$)im',
            ParsedRegex::TYPE_EQUALS,
            'text',
            '(',
            'im',
        ];

        yield 'escaped dot converts to text' => [
            '/^foo\\.bar$/',
            ParsedRegex::TYPE_EQUALS,
            'foo.bar',
            '/',
            '',
        ];

        yield 'escaped special chars' => [
            '/^foo\\+bar\\|baz$/',
            ParsedRegex::TYPE_EQUALS,
            'foo+bar|baz',
            '/',
            '',
        ];

        yield 'escaped delimiter' => [
            '/^\\/path\\/to\\/file$/',
            ParsedRegex::TYPE_EQUALS,
            '/path/to/file',
            '/',
            '',
        ];

        yield 'multiple modifiers' => [
            '/^multiline$/imsx',
            ParsedRegex::TYPE_EQUALS,
            'multiline',
            '/',
            'imsx',
        ];

        yield 'metachar pipe cannot convert' => [
            '/foo|bar/',
            ParsedRegex::TYPE_REGEX,
            'foo|bar',
            '/',
            '',
        ];

        yield 'metachar plus cannot convert' => [
            '/^pattern+$/',
            ParsedRegex::TYPE_REGEX,
            '^pattern+$',
            '/',
            '',
        ];

        yield 'character class cannot convert' => [
            '/[a-z]+/',
            ParsedRegex::TYPE_REGEX,
            '[a-z]+',
            '/',
            '',
        ];

        yield 'grouping cannot convert' => [
            '/(foo)(bar)/',
            ParsedRegex::TYPE_REGEX,
            '(foo)(bar)',
            '/',
            '',
        ];

        yield 'empty pattern' => [
            '//',
            ParsedRegex::TYPE_CONTAINS,
            '',
            '/',
            '',
        ];

        yield 'empty pattern with anchors' => [
            '/^$/',
            ParsedRegex::TYPE_EQUALS,
            '',
            '/',
            '',
        ];

        yield 'escaped backslash before dollar' => [
            '/text\\\\$/',
            ParsedRegex::TYPE_ENDSWITH,
            'text\\',
            '/',
            '',
        ];

        yield 'multiple escaped chars' => [
            '/^\\.\\+\\*\\?$/',
            ParsedRegex::TYPE_EQUALS,
            '.+*?',
            '/',
            '',
        ];

        yield 'all modifiers' => [
            '/^test$/imsxADUXJu',
            ParsedRegex::TYPE_EQUALS,
            'test',
            '/',
            'imsxADUXJu',
        ];

        yield 'text with spaces equals' => [
            '/^  spaces  $/',
            ParsedRegex::TYPE_EQUALS,
            '  spaces  ',
            '/',
            '',
        ];

        yield 'text with spaces contains' => [
            '/text with spaces/',
            ParsedRegex::TYPE_CONTAINS,
            'text with spaces',
            '/',
            '',
        ];

        yield 'escaped hash delimiter' => [
            '#^foo\\#bar$#',
            ParsedRegex::TYPE_EQUALS,
            'foo#bar',
            '#',
            '',
        ];

        yield 'escaped space' => [
            '/foo\ bar/Di',
            ParsedRegex::TYPE_REGEX,
            'foo\ bar',
            '/',
            'Di',
        ];
    }
}
