<?php

namespace Concrete\Core\Database\CharacterSetCollation\Exception;

use Concrete\Core\Database\CharacterSetCollation\Exception;

class UnsupportedCharacterSetException extends Exception
{
    /**
     * @var string
     */
    protected $unsupportedCharacterSet;

    /**
     * @param string $unsupportedCharacterSet
     */
    public function __construct($unsupportedCharacterSet)
    {
        $this->unsupportedCharacterSet = $unsupportedCharacterSet;
        parent::__construct(t('The character set "%s" is not supported.', $this->getUnsupportedCharacterSet()));
    }

    /**
     * @return string
     */
    public function getUnsupportedCharacterSet()
    {
        return $this->unsupportedCharacterSet;
    }
}
