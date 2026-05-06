<?php

declare(strict_types=1);

namespace Concrete\Core\Utility\RegexParser;

final class ParsedRegex implements \JsonSerializable
{
    public const TYPE_REGEX = 'regex';

    public const TYPE_EQUALS = 'equals';

    public const TYPE_STARTSWITH = 'startsWith';

    public const TYPE_ENDSWITH = 'endsWith';

    public const TYPE_CONTAINS = 'contains';

    /**
     * @see https://www.php.net/manual/en/reference.pcre.pattern.modifiers.php
     */
    public const MODIFIER_CASELESS = 'i';

    public const MODIFIER_MULTILINE = 'm';

    public const MODIFIER_DOTALL = 's';

    public const MODIFIER_EXTENDED = 'x';

    public const MODIFIER_ANCHORED = 'A';

    public const MODIFIER_DOLLAR_ENDONLY = 'D';

    public const MODIFIER_UNGREEDY = 'U';

    public const MODIFIER_EXTRA = 'X';

    public const MODIFIER_INFO_JCHANGED = 'J';

    public const MODIFIER_UTF8 = 'u';

    /**
     * @since PHP 8.2
     */
    public const MODIFIER_NO_AUTO_CAPTURE = 'n';

    /**
     * @since PHP 8.3
     */
    public const MODIFIER_EXTRA_CASELESS_RESTRICT = 'r';

    /**
     * @see https://www.php.net/manual/en/regexp.reference.delimiters.php
     */
    public const DELIMITER_COUPLES = [
        '(' => ')',
        '{' => '}',
        '[' => ']',
        '<' => '>',
    ];

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $delimiter;

    /**
     * @var string
     */
    private $modifiers;

    public function __construct(string $type, string $text, string $delimiter = '/', string $modifiers = '')
    {
        $this->type = $type;
        $this->text = $text;
        $this->delimiter = $delimiter;
        $this->modifiers = $modifiers;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function withType(string $newType): self
    {
        $result = clone $this;
        $result->type = $newType;

        return $result;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function withText(string $newText): self
    {
        $result = clone $this;
        $result->text = $newText;

        return $result;
    }

    public function getDelimiter(): string
    {
        return $this->delimiter;
    }

    public function withDelimiter(string $newDelimiter): self
    {
        $result = clone $this;
        $result->delimiter = $newDelimiter;

        return $result;
    }

    public function getModifiers(): string
    {
        return $this->modifiers;
    }

    public function withModifiers(string $newModifiers): self
    {
        $result = clone $this;
        $result->modifiers = $newModifiers;

        return $result;
    }

    public function asRegex(): string
    {
        $result = $this->delimiter;
        if ($this->type === self::TYPE_REGEX) {
            $result .= $this->text;
        } else {
            if (in_array($this->type, [self::TYPE_STARTSWITH, self::TYPE_EQUALS], true)) {
                $result .= '^';
            }
            $result .= preg_quote($this->text, $this->delimiter);
            if (in_array($this->type, [self::TYPE_ENDSWITH, self::TYPE_EQUALS], true)) {
                $result .= '$';
            }
        }
        $result .= self::DELIMITER_COUPLES[$this->delimiter] ?? $this->delimiter;
        $result .= $this->modifiers;

        return $result;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'text' => $this->text,
            'delimiter' => $this->delimiter,
            'modifiers' => $this->modifiers,
        ];
    }

    /**
     * @return string[]
     */
    public static function getAllModifiers(): array
    {
        static $result;
        if ($result === null) {
            $result = [];
            $ref = new \ReflectionClass(self::class);
            foreach ($ref->getConstants() as $name => $value) {
                if (!str_starts_with($name, 'MODIFIER_')) {
                    continue;
                }
                if ($name === 'MODIFIER_NO_AUTO_CAPTURE' && PHP_VERSION_ID < 80200) {
                    continue;
                }
                if ($name === 'MODIFIER_EXTRA_CASELESS_RESTRICT' && PHP_VERSION_ID < 80400) {
                    continue;
                }
                $result[] = $value;
            }
        }

        return $result;
    }
}
