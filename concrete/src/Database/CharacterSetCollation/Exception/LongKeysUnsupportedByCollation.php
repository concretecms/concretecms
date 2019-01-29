<?php

namespace Concrete\Core\Database\CharacterSetCollation\Exception;

use Concrete\Core\Database\CharacterSetCollation\Exception;

class LongKeysUnsupportedByCollation extends Exception
{
    /**
     * @var string
     */
    protected $collation;

    /**
     * @var int
     */
    protected $wantedKeyLength;

    /**
     * @param string $collation
     * @param int $wantedKeyLength
     */
    public function __construct($collation, $wantedKeyLength)
    {
        $this->collation = $collation;
        $this->wantedKeyLength = $wantedKeyLength;
        parent::__construct(t(
            'The database does not support index fields with a length up to %1$s characters when using the collation "%2$s".',
            $this->getWantedKeyLength(),
            $this->getCollation()
        ));
    }

    /**
     * @return string
     */
    public function getCollation()
    {
        return $this->collation;
    }

    /**
     * @return int
     */
    public function getWantedKeyLength()
    {
        return $this->wantedKeyLength;
    }
}
