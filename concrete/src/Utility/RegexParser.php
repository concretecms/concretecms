<?php

declare(strict_types=1);

namespace Concrete\Core\Utility;

use Concrete\Core\Utility\RegexParser\ParsedRegex;

class RegexParser
{
    /**
     * @see https://www.php.net/manual/en/regexp.reference.meta.php
     */
    private const META_CHARS = ['\\', '^', '$', '.', '[', ']', '|', '(', ')', '?', '*', '+', '{', '}'];

    /**
     * preg_quote() also escaped these chars ('#' since PHP 7.3, '-' since PHP 5.3)
     */
    private const ADDITIONAL_ESCAPED_CHARS = ['!', '#', '-', ':', '<', '=', '>'];

    /**
     * @throws \RuntimeException
     */
    public function parseRegEx(string $regex): ParsedRegex
    {
        $result = new ParsedRegex(ParsedRegex::TYPE_REGEX, $regex);
        $parts = $this->splitRegex($regex);
        if ($parts === null) {
            throw new \RuntimeException(t('Invalid regular expression'));
        }
        $result = $result
            ->withText($parts['pattern'])
            ->withDelimiter($parts['delimiter'])
            ->withModifiers($parts['modifiers'])
        ;
        $hasStart = $this->patternStartsWithCaret($parts['pattern']);
        $hasEnd = $this->patternEndsWithDollar($parts['pattern']);
        $cleanPattern = $parts['pattern'];
        if ($hasStart) {
            $cleanPattern = substr($cleanPattern, 1);
        }
        if ($hasEnd) {
            $cleanPattern = substr($cleanPattern, 0, -1);
        }
        if ($this->isConvertibleToTextSearch($cleanPattern, $result->getDelimiter())) {
            $result = $result->withText($this->unescapeSimplePattern($cleanPattern, $result->getDelimiter()));
            if ($hasStart && $hasEnd) {
                $result = $result->withType(ParsedRegex::TYPE_EQUALS);
            } elseif ($hasStart) {
                $result = $result->withType(ParsedRegex::TYPE_STARTSWITH);
            } elseif ($hasEnd) {
                $result = $result->withType(ParsedRegex::TYPE_ENDSWITH);
            } else {
                $result = $result->withType(ParsedRegex::TYPE_CONTAINS);
            }
        }

        return $result;
    }

    /**
     * @return array{'delimiter': string, 'pattern': string, 'modifiers': string}
     */
    private function splitRegex(string $regex): ?array
    {
        set_error_handler(static function () { }, -1);
        try {
            if (preg_match($regex, '') === false) {
                return null;
            }
        } finally {
            restore_error_handler();
        }
        $matches = null;
        if (!preg_match(
            '/^(?<delimiter1>[^A-Za-z0-9\s\\\\])(?<pattern>.*)(?<delimiter2>[^A-Za-z0-9\s\\\\])(?<modifiers>[' . implode('', ParsedRegex::getAllModifiers()) . ']*)$/D',
            $regex,
            $matches
        )) {
            return null;
        }
        $expectedDelimiter2 = ParsedRegex::DELIMITER_COUPLES[$matches['delimiter1']] ?? $matches['delimiter1'];
        if ($matches['delimiter2'] !== $expectedDelimiter2) {
            return null;
        }

        return [
            'delimiter' => $matches['delimiter1'],
            'pattern' => $matches['pattern'],
            'modifiers' => $matches['modifiers'],
        ];
    }

    private function patternStartsWithCaret(string $pattern): bool
    {
        return $pattern !== '' && $pattern[0] === '^';
    }

    private function patternEndsWithDollar(string $pattern): bool
    {
        // Read it as:
        // - '\\$$': the string ends with a $
        // - '(?:\\\\\\\\)*': before the trailing $ we may have 0, 2, 4, 6, ... an even number of '\'
        // - '(?<!\\\\)': the character immediately before the whole trailing sequence must not be a '\'
        // In short: the string ends with a '$' which is not escaped by an odd number of backslashes
        return (bool) preg_match('/(?<!\\\\)(?:\\\\\\\\)*\\$$/D', $pattern);
    }

    private function isConvertibleToTextSearch(string $pattern, string $delimiter): bool
    {
        $charsAfterBackslash = implode('', self::META_CHARS) . implode('', self::ADDITIONAL_ESCAPED_CHARS) . $delimiter;
        $i = 0;
        $len = strlen($pattern);
        while ($i < $len) {
            $char = $pattern[$i];
            if ($char === '\\') {
                if ($i + 1 < $len) {
                    $nextChar = $pattern[$i + 1];
                    if (strpos($charsAfterBackslash, $nextChar) === false) {
                        return false;
                    }
                    $i += 2;
                    continue;
                }
                return false;
            }
            if (in_array($char, self::META_CHARS, true)) {
                // Unescaped meta character: not a simple regex
                return false;
            }
            $i++;
        }

        return true;
    }

    private function unescapeSimplePattern(string $pattern, string $delimiter): string
    {
        return preg_replace('/\\\\(.)/', '$1', $pattern);
    }
}
