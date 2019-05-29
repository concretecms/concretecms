<?php

namespace Concrete\Core\Database\CharacterSetCollation;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;

class Resolver
{
    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $config;

    /**
     * The character set.
     *
     * @var string|null NULL to use default character set
     */
    protected $characterSet;

    /**
     * The collation.
     *
     * @var string|null NULL to use the default collation
     */
    protected $collation;

    /**
     * The maximum length of string fields that should be supported using the specified collation.
     *
     * @var int
     */
    protected $maximumStringKeyLength = 255;

    /**
     * @param \Concrete\Core\Config\Repository\Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * Get the configuration instance.
     *
     * @return \Concrete\Core\Config\Repository\Repository
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the default character set.
     *
     * @return string Empty string if not set
     */
    public function getDefaultCharacterSet()
    {
        $characterSet = $this->config->get('database.preferred_character_set');
        $characterSet = $this->normalizeCharacterSet($characterSet);

        return $characterSet;
    }

    /**
     * Get the character set.
     *
     * @return string Empty string if not set
     */
    public function getCharacterSet()
    {
        return $this->characterSet === null ? $this->getDefaultCharacterSet() : $this->characterSet;
    }

    /**
     * Set the character set.
     *
     * @param string|null|mixed $characterSet use NULL to use the default character set
     *
     * @return $this
     */
    public function setCharacterSet($characterSet)
    {
        if ($characterSet !== null) {
            $characterSet = $this->normalizeCharacterSet($characterSet);
        }
        $this->characterSet = $characterSet;

        return $this;
    }

    /**
     * Get the default collation.
     *
     * @return string Empty string if not set
     */
    public function getDefaultCollation()
    {
        $collation = $this->config->get('database.preferred_collation');
        $collation = $this->normalizeCollation($collation);

        return $collation;
    }

    /**
     * Get the collation.
     *
     * @return string Empty string if not set
     */
    public function getCollation()
    {
        return $this->collation === null ? $this->getDefaultCollation() : $this->collation;
    }

    /**
     * Set the collation.
     *
     * @param string|null|mixed $collation use NULL to use the default collation
     *
     * @return $this
     */
    public function setCollation($collation)
    {
        if ($collation !== null) {
            $collation = $this->normalizeCollation($collation);
        }
        $this->collation = $collation;

        return $this;
    }

    /**
     * Get the maximum length of string fields that should be supported using the specified collation.
     *
     * @return int
     */
    public function getMaximumStringKeyLength()
    {
        return $this->maximumStringKeyLength;
    }

    /**
     * Set the maximum length of string fields that should be supported using the specified collation.
     *
     * @param int $value
     *
     * @return $this
     */
    public function setMaximumStringKeyLength($value)
    {
        $this->maximumStringKeyLength = (int) $value;

        return $this;
    }

    /**
     * Resolve the character set and collation.
     *
     * @param \Concrete\Core\Database\Connection\Connection $connection
     *
     * @throws \Concrete\Core\Database\CharacterSetCollation\Exception\NoCharacterSetCollationDefinedException
     * @throws \Concrete\Core\Database\CharacterSetCollation\Exception\UnsupportedCharacterSetException
     * @throws \Concrete\Core\Database\CharacterSetCollation\Exception\UnsupportedCollationException
     * @throws \Concrete\Core\Database\CharacterSetCollation\Exception\InvalidCharacterSetCollationCombination
     * @throws \Concrete\Core\Database\CharacterSetCollation\Exception\LongKeysUnsupportedByCollation
     *
     * return string[] first value is the character set; the second value is the collation
     */
    public function resolveCharacterSetAndCollation(Connection $connection)
    {
        $characterSet = $this->getCharacterSet();
        $collation = $this->getCollation();
        if ($collation !== '') {
            $collations = $connection->getSupportedCollations();
            if (!isset($collations[$collation])) {
                throw new Exception\UnsupportedCollationException($collation);
            }
            if ($characterSet === '') {
                $characterSet = $collations[$collation];
            } elseif ($characterSet !== $collations[$collation]) {
                throw new Exception\InvalidCharacterSetCollationCombination($characterSet, $collation, $collations[$characterSet]);
            }
        } elseif ($characterSet !== '') {
            $characterSets = $connection->getSupportedCharsets();
            if (!isset($characterSets[$characterSet])) {
                throw new Exception\UnsupportedCharacterSetException($characterSet);
            }
            $collation = $characterSets[$characterSet];
        } else {
            throw new Exception\NoCharacterSetCollationDefinedException();
        }
        if (!$connection->isCollationSupportedForKeys($collation, $this->getMaximumStringKeyLength())) {
            throw new Exception\LongKeysUnsupportedByCollation($collation, $this->getMaximumStringKeyLength());
        }

        return [$characterSet, $collation];
    }

    /**
     * Get the normalized form (lower case, only letters/digits/underscores) of a character set.
     *
     * @param string|mixed $characterSet
     *
     * @return string empty string if $characterSet is not valid
     */
    public function normalizeCharacterSet($characterSet)
    {
        return is_string($characterSet) && preg_match('/^\w+$/', $characterSet) ? strtolower($characterSet) : '';
    }

    /**
     * Get the normalized form (lower case, only letters/digits/underscores) of a collation.
     *
     * @param string|mixed $collation
     *
     * @return string empty string if $collation is not valid
     */
    public function normalizeCollation($collation)
    {
        return is_string($collation) && preg_match('/^\w+$/', $collation) ? strtolower($collation) : '';
    }
}
