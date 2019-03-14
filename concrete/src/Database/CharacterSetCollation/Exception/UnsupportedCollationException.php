<?php

namespace Concrete\Core\Database\CharacterSetCollation\Exception;

use Concrete\Core\Database\CharacterSetCollation\Exception;

class UnsupportedCollationException extends Exception
{
    /**
     * @var string
     */
    protected $unsupportedCollation;

    /**
     * @param string $unsupportedCollation
     */
    public function __construct($unsupportedCollation)
    {
        $this->unsupportedCollation = $unsupportedCollation;
        parent::__construct(t('The collation "%s" is not supported.', $this->getUnsupportedCollation()));
    }

    /**
     * @return string
     */
    public function getUnsupportedCollation()
    {
        return $this->unsupportedCollation;
    }
}
