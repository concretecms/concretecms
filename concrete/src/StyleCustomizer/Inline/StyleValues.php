<?php

declare(strict_types=1);

namespace Concrete\Core\StyleCustomizer\Inline;

use Concrete\Core\Error\UserMessageException;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * The values that the design of an area or of a block takes, and the rules whatever comes from outside is
 * held to: the design dialog offers these, the API takes these, and a CIF is read for these.
 *
 * A sanitizer gives back the value that holds, and an empty string where none does, which is what a field
 * nobody set is worth.
 *
 * Whoever wants other ones binds a class of their own in place of this one.
 */
class StyleValues
{
    /**
     * The ways a background image repeats, by the name each goes under.
     *
     * @return array<string,string>
     */
    public function getBackgroundRepeats(): array
    {
        return [
            'no-repeat' => tc('BackgroundRepeat', 'No Repeat'),
            'repeat-x' => tc('BackgroundRepeat', 'Horizontally'),
            'repeat-y' => tc('BackgroundRepeat', 'Vertically'),
            'repeat' => tc('BackgroundRepeat', 'Horizontally & Vertically'),
        ];
    }

    public function sanitizeBackgroundRepeat($value): string
    {
        return $this->sanitizeEnum($value, array_keys($this->getBackgroundRepeats()));
    }

    /**
     * The sizes a background image is drawn at, by the name each goes under.
     *
     * @return array<string,string>
     */
    public function getBackgroundSizes(): array
    {
        return [
            'auto' => tc('BackgroundSize', 'Auto'),
            'contain' => tc('BackgroundSize', 'Contain'),
            'cover' => tc('BackgroundSize', 'Cover'),
            '10%' => tc('BackgroundSize', '10%'),
            '25%' => tc('BackgroundSize', '25%'),
            '50%' => tc('BackgroundSize', '50%'),
            '75%' => tc('BackgroundSize', '75%'),
            '100%' => tc('BackgroundSize', '100%'),
        ];
    }

    public function sanitizeBackgroundSize($value): string
    {
        return $this->sanitizeEnum($value, array_keys($this->getBackgroundSizes()));
    }

    /**
     * The places a background image is drawn at, by the name each goes under.
     *
     * @return array<string,string>
     */
    public function getBackgroundPositions(): array
    {
        return [
            'left top' => tc('BackgroundPosition', 'Left Top'),
            'left center' => tc('BackgroundPosition', 'Left Center'),
            'left bottom' => tc('BackgroundPosition', 'Left Bottom'),
            'center top' => tc('BackgroundPosition', 'Center Top'),
            'center center' => tc('BackgroundPosition', 'Center Center'),
            'center bottom' => tc('BackgroundPosition', 'Center Bottom'),
            'right top' => tc('BackgroundPosition', 'Right Top'),
            'right center' => tc('BackgroundPosition', 'Right Center'),
            'right bottom' => tc('BackgroundPosition', 'Right Bottom'),
        ];
    }

    public function sanitizeBackgroundPosition($value): string
    {
        return $this->sanitizeEnum($value, array_keys($this->getBackgroundPositions()));
    }

    /**
     * The ways a border is drawn, by the name each goes under, the empty one being no border of its own.
     *
     * @return array<string,string>
     */
    public function getBorderStyles(): array
    {
        return [
            '' => tc('BorderStyle', 'None'),
            'solid' => tc('BorderStyle', 'Solid'),
            'dotted' => tc('BorderStyle', 'Dotted'),
            'dashed' => tc('BorderStyle', 'Dashed'),
            'double' => tc('BorderStyle', 'Double'),
            'groove' => tc('BorderStyle', 'Groove'),
            'ridge' => tc('BorderStyle', 'Ridge'),
            'inset' => tc('BorderStyle', 'Inset'),
            'outset' => tc('BorderStyle', 'Outset'),
        ];
    }

    public function sanitizeBorderStyle($value): string
    {
        return $this->sanitizeEnum($value, array_keys($this->getBorderStyles()));
    }

    /**
     * The ways the contents are aligned, by the name each goes under, the empty one leaving them to the theme.
     *
     * @return array<string,string>
     */
    public function getAlignments(): array
    {
        return [
            '' => tc('Alignment', 'None'),
            'left' => tc('Alignment', 'Left'),
            'center' => tc('Alignment', 'Center'),
            'right' => tc('Alignment', 'Right'),
        ];
    }

    public function sanitizeAlignment($value): string
    {
        return $this->sanitizeEnum($value, array_keys($this->getAlignments()));
    }

    /**
     * @param string[] $allowed the values of one of the lists above
     */
    public function sanitizeEnum($value, array $allowed): string
    {
        $value = trim((string) $value);

        return in_array($value, $allowed, true) ? $value : '';
    }

    /**
     * @param string[] $cssClasses
     *
     * @return string the classes that hold, separated by a space
     */
    public function sanitizeClasses(array $cssClasses): string
    {
        $cssClasses = array_filter($cssClasses, static function ($class) {
            return preg_match('/^[^<>\'"]+$/D', $class);
        });

        return implode(' ', $cssClasses);
    }

    public function sanitizeColor($value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }
        if (preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/iD', $value)) {
            return $value;
        }
        if (preg_match('/^(?:rgb|hsl)a?\(\s*-?(?:\d+|\d*\.\d+)%?(?:(?:\s*[,\/]\s*|\s+)-?(?:\d+|\d*\.\d+)%?){2,3}\s*\)$/iD', $value)) {
            return (string) preg_replace('/\s+/', ' ', $value);
        }

        return in_array(strtolower($value), ['transparent', 'currentcolor'], true) ? $value : '';
    }

    /**
     * A length carries its unit, apart from a length of nothing, the only bare number CSS takes.
     *
     * @param bool $allowZero whether a length of nothing is one: the design dialog sends one for every
     *     slider nobody has touched, and means nothing by it
     */
    public function sanitizeLength($value, bool $allowNegative = true, bool $allowZero = true): string
    {
        $pattern = $allowNegative
            ? '/^(?<number>-?(?:\d+|\d*\.\d+))(?<unit>px|%|em|rem|vh|vw|vmin|vmax)?$/iD'
            : '/^(?<number>(?:\d+|\d*\.\d+))(?<unit>px|%|em|rem|vh|vw|vmin|vmax)?$/iD';
        if (!preg_match($pattern, trim((string) $value), $matches)) {
            return '';
        }
        $unit = $matches['unit'] ?? '';
        if ((float) $matches['number'] === 0.0) {
            return $allowZero ? $matches['number'] . $unit : '';
        }

        return $unit === '' ? '' : $matches['number'] . $unit;
    }

    /**
     * The class and the ID have fields of their own, so they are stripped here: the rest is written on the
     * element as it is.
     *
     * @throws \Concrete\Core\Error\UserMessageException when the quotes in it are not closed
     */
    public function sanitizeElementAttribute($value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }
        // the name has to end there, or an attribute of another name loses a piece of itself
        $value = (string) preg_replace('/(?<![-\w])class\s*=\s*["\'][^\'"]*["\']/i', '', $value);
        $value = (string) preg_replace('/(?<![-\w])id\s*=\s*["\'][^\'"]*["\']/i', '', $value);
        if (preg_match_all('/([\'])/i', $value) % 2 !== 0 || preg_match_all('/(["])/i', $value) % 2 !== 0) {
            throw new UserMessageException(t('Custom Element Attribute input: unclosed quote(s)'));
        }

        return trim($value);
    }

    /**
     * @param bool $allowZero whether turning by nothing is a rotation: the design dialog sends one where
     *     nobody has turned anything, and means nothing by it
     *
     * @return string the degrees to turn by
     */
    public function sanitizeRotation($value, bool $allowZero = true): string
    {
        $value = trim((string) $value);
        if (!preg_match('/^-?(?:\d+|\d*\.\d+)$/D', $value)) {
            return '';
        }

        return !$allowZero && (float) $value === 0.0 ? '' : $value;
    }
}
