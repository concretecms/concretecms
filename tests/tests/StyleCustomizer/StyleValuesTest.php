<?php

declare(strict_types=1);

namespace Concrete\Tests\StyleCustomizer;

use Concrete\Core\Error\UserMessageException;
use Concrete\Core\StyleCustomizer\Inline\StyleValues;
use Concrete\Tests\TestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * The values a design takes and the rules whatever comes from outside is held to: a value that is not a CSS
 * value of its kind is dropped, and what is left is an empty string.
 *
 * @see \Concrete\Core\StyleCustomizer\Inline\StyleValues
 */
class StyleValuesTest extends TestCase
{
    /**
     * Whoever offers a value has to take it back, so the lists and the sanitizers say the same thing.
     *
     * @dataProvider provideLists
     */
    public function testTheValuesOfAListAreTheOnesItTakesBack(string $list, string $sanitizer): void
    {
        $styleValues = new StyleValues();

        foreach (array_keys($styleValues->{$list}()) as $value) {
            static::assertSame($value, $styleValues->{$sanitizer}($value), $list . ' offers ' . var_export($value, true));
        }
        static::assertSame('', $styleValues->{$sanitizer}('sideways'));
    }

    /**
     * @return array<array{string,string}>
     */
    public static function provideLists(): array
    {
        return [
            ['getBackgroundRepeats', 'sanitizeBackgroundRepeat'],
            ['getBackgroundSizes', 'sanitizeBackgroundSize'],
            ['getBackgroundPositions', 'sanitizeBackgroundPosition'],
            ['getBorderStyles', 'sanitizeBorderStyle'],
            ['getAlignments', 'sanitizeAlignment'],
        ];
    }

    /**
     * @dataProvider provideColors
     */
    public function testAColorIsWrittenAsCssWritesOne(string $value, string $expected): void
    {
        static::assertSame($expected, (new StyleValues())->sanitizeColor($value));
    }

    /**
     * @return array<array{string,string}>
     */
    public static function provideColors(): array
    {
        return [
            ['#abc', '#abc'],
            ['#AABBCCDD', '#AABBCCDD'],
            ['  rgb(1,2,3)  ', 'rgb(1,2,3)'],
            ['rgba(255,  0,0,   .5)', 'rgba(255, 0,0, .5)'],
            ['rgb(1 2 3)', 'rgb(1 2 3)'],
            ['rgb(1 2 3 / 50%)', 'rgb(1 2 3 / 50%)'],
            ['hsl(120, 75%, 25%)', 'hsl(120, 75%, 25%)'],
            ['transparent', 'transparent'],
            ['currentColor', 'currentColor'],
            // a name is not one of them, however much of a color it is
            ['red', ''],
            // the parentheses hold channels, not whatever looks like one
            ['rgb(,,,,)', ''],
            ['rgb(1,2)', ''],
            ['rgb(-,-)', ''],
            ['#ff00', '#ff00'],
            ['#fffff', ''],
            ['rgb(1,2,3', ''],
            ['rgb(1,2,3);background:url(x)', ''],
            ['', ''],
        ];
    }

    /**
     * @dataProvider provideLengths
     */
    public function testALengthIsANumberWithItsUnit(string $value, bool $allowNegative, bool $allowZero, string $expected): void
    {
        static::assertSame($expected, (new StyleValues())->sanitizeLength($value, $allowNegative, $allowZero));
    }

    /**
     * @return array<array{string,bool,bool,string}>
     */
    public static function provideLengths(): array
    {
        return [
            ['10px', true, true, '10px'],
            ['1.5rem', true, true, '1.5rem'],
            ['50%', true, true, '50%'],
            ['.5em', true, true, '.5em'],
            ['-10px', true, true, '-10px'],
            ['-10px', false, true, ''],
            // CSS takes a bare number only where it is a length of nothing
            ['12', true, true, ''],
            ['0', true, true, '0'],
            ['0', true, false, ''],
            // a length of nothing is one only to whoever asks for it
            ['0px', true, true, '0px'],
            ['0px', true, false, ''],
            ['0rem', true, false, ''],
            ['0.00%', true, false, ''],
            ['-0px', true, false, ''],
            ['10 miles', true, true, ''],
            ['10pt', true, true, ''],
            ['10px;color:red', true, true, ''],
            ['', true, true, ''],
        ];
    }

    public function testARotationIsPlainDegrees(): void
    {
        $styleValues = new StyleValues();

        static::assertSame('45', $styleValues->sanitizeRotation('45'));
        static::assertSame('-4.5', $styleValues->sanitizeRotation('-4.5'));
        static::assertSame('0', $styleValues->sanitizeRotation('0'));
        static::assertSame('', $styleValues->sanitizeRotation('0', false));
        static::assertSame('', $styleValues->sanitizeRotation('0.00', false));
        static::assertSame('', $styleValues->sanitizeRotation('45deg'));
        static::assertSame('', $styleValues->sanitizeRotation('turn it'));
        static::assertSame('', $styleValues->sanitizeRotation(''));
    }

    public function testTheClassesThatHoldAreTheOnesKept(): void
    {
        $styleValues = new StyleValues();

        static::assertSame('hero md:text-left', $styleValues->sanitizeClasses(['hero', '"><script>', 'md:text-left']));
        static::assertSame('', $styleValues->sanitizeClasses(['<nothere>']));
        static::assertSame('', $styleValues->sanitizeClasses([]));
    }

    /**
     * The class and the ID of the element have fields of their own, so an attribute naming them again is
     * asking for what those fields are for.
     */
    public function testTheClassAndTheIdAreNoPartOfTheAttribute(): void
    {
        $styleValues = new StyleValues();

        static::assertSame('data-x="1"', $styleValues->sanitizeElementAttribute('class="sneaky" data-x="1"'));
        static::assertSame('data-x="1"', $styleValues->sanitizeElementAttribute("id='sneaky' data-x=\"1\""));
        static::assertSame('', $styleValues->sanitizeElementAttribute('class="sneaky"'));
        static::assertSame('', $styleValues->sanitizeElementAttribute('   '));
        // an attribute whose name ends in them keeps what it carries
        static::assertSame('data-class="hero"', $styleValues->sanitizeElementAttribute('data-class="hero"'));
        static::assertSame('data-id="7"', $styleValues->sanitizeElementAttribute('data-id="7"'));
    }

    public function testAnAttributeWhoseQuotesAreNotClosedIsRefused(): void
    {
        $this->expectException(UserMessageException::class);

        (new StyleValues())->sanitizeElementAttribute('data-x="1');
    }
}
