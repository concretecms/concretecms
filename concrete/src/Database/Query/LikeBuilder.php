<?php
namespace Concrete\Core\Database\Query;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class LikeBuilder
{
    /**
     * The default wildcard that matches any number of characters in a LIKE query.
     *
     * @var string
     */
    const DEFAULT_ANYCHARACTER_WILDCARD = '%';

    /**
     * The default wildcard that matches exactly one character in a LIKE query.
     *
     * @var string
     */
    const DEFAULT_ONECHARACTER_WILDCARD = '_';

    /**
     * The default character used to escape wildcards and other special characters.
     *
     * @var string
     */
    const DEFAULT_ESCAPE_CHARACTER = '\\';

    /**
     * The wildcard that matches any number of characters in a LIKE query.
     *
     * @var string
     */
    protected $anyCharacterWildcard;

    /**
     * The wildcard that matches exactly one character in a LIKE query.
     *
     * @var string
     */
    protected $oneCharacterWildcard;

    /**
     * Any other characters that may have a special meaning in a LIKE query.
     *
     * @var string[]
     */
    protected $otherWildcards;

    /**
     * The character used to escape wildcards and other special characters.
     *
     * @var string
     */
    protected $escapeCharacter;

    /**
     * The string mapping used to escape special characters.
     *
     * @var array|null
     */
    protected $escapeMap = null;

    /**
     * Initialize the instance.
     *
     * @param AbstractPlatform $platform the database platform
     */
    public function __construct($anyCharacterWildcard = self::DEFAULT_ANYCHARACTER_WILDCARD, $oneCharacterWildcard = self::DEFAULT_ONECHARACTER_WILDCARD, $escapeCharacter = self::DEFAULT_ESCAPE_CHARACTER, array $otherWildcards = [])
    {
        $this->anyCharacterWildcard = $anyCharacterWildcard;
        $this->oneCharacterWildcard = $oneCharacterWildcard;
        $this->otherWildcards = $otherWildcards;
        $this->escapeCharacter = $escapeCharacter;
    }

    /**
     * Get the wildcard that matches any number of characters in a LIKE query.
     *
     * @return string
     */
    public function getAnyCharacterWildcard()
    {
        return $this->anyCharacterWildcard;
    }

    /**
     * Get the wildcard that matches exactly one character in a LIKE query.
     *
     * @return string
     */
    public function getOneCharacterWildcard()
    {
        return $this->oneCharacterWildcard;
    }

    /**
     * Get the string mapping used to escape special characters.
     *
     * @return array
     */
    protected function getEscapeMap()
    {
        if ($this->escapeMap === null) {
            $escapeMap = [
                $this->anyCharacterWildcard => $this->escapeCharacter . $this->anyCharacterWildcard,
                $this->oneCharacterWildcard => $this->escapeCharacter . $this->oneCharacterWildcard,
                $this->escapeCharacter => $this->escapeCharacter . $this->escapeCharacter,
            ];
            foreach ($this->otherWildcards as $otherWildcard) {
                $escapeMap[$otherWildcard] = $this->escapeCharacter . $otherWildcard;
            }
            $this->escapeMap = $escapeMap;
        }

        return $this->escapeMap;
    }

    /**
     * Escape a string to be safely used as a parameter for a LIKE query.
     *
     * @param string $string The string to be escaped
     * @param bool $wildcardAtStart whether to add the any-character wildcard as a prefix
     * @param bool $wildcardAtEnd whether to add the any-character wildcard as a suffix
     *
     * @return string
     *
     * @example escapeForLike('Hi% there', false, true) will return 'Hi\% there%'
     */
    public function escapeForLike($string, $wildcardAtStart = true, $wildcardAtEnd = true)
    {
        if ($wildcardAtStart) {
            $result = $this->anyCharacterWildcard;
        } else {
            $result = '';
        }
        $result .= strtr((string) $string, $this->getEscapeMap());
        if ($wildcardAtEnd && $result !== $this->anyCharacterWildcard) {
            $result .= $this->anyCharacterWildcard;
        }

        return $result;
    }

    /**
     * Split a string into words and format them to be used in LIKE queries.
     *
     * @param string|mixed $string The string to be splitted
     * @param string $wordSeparators The regular expression pattern that represents potential word delimiters (eg '\s' for any whitespace character)
     * @param bool $addWildcards whether to add any-character wildcard at start and end of every resulting word
     *
     * @return string[]|null Returns null if no word has been found, an array of escaped words otherwise
     */
    public function splitKeywordsForLike($string, $wordSeparators = '\s', $addWildcards = true)
    {
        $result = null;
        if (is_string($string)) {
            $words = preg_split('/[' . $wordSeparators . ']+/ms', $string);
            foreach ($words as $word) {
                if ($word !== '') {
                    $result[] = $this->escapeForLike($word, $addWildcards, $addWildcards);
                }
            }
        }

        return $result;
    }
}
