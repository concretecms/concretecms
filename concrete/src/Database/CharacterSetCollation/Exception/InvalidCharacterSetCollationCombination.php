<?php

namespace Concrete\Core\Database\CharacterSetCollation\Exception;

use Concrete\Core\Database\CharacterSetCollation\Exception;

class InvalidCharacterSetCollationCombination extends Exception
{
    /**
     * @var string
     */
    protected $characterSet;

    /**
     * @var string
     */
    protected $collation;

    /**
     * @var string
     */
    protected $charsetForCollation;

    /**
     * @param string $characterSet
     * @param string $collation
     * @param string $charsetForCollation
     */
    public function __construct($characterSet, $collation, $charsetForCollation)
    {
        $this->characterSet = $characterSet;
        $this->collation = $collation;
        $this->charsetForCollation = $charsetForCollation;
        parent::__construct(t(
            'The collation "%1$s" is associated to the character set "%2$s" and not to the character set "%3$s".',
            $this->getCollation(),
            $this->getCharsetForCollation(),
            $this->getCharacterSet()
        ));
    }

    /**
     * @return string
     */
    public function getCharacterSet()
    {
        return $this->characterSet;
    }

    /**
     * @return string
     */
    public function getCollation()
    {
        return $this->collation;
    }

    /**
     * @return string
     */
    public function getCharsetForCollation()
    {
        return $this->charsetForCollation;
    }
}
