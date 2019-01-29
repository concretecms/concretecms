<?php

namespace Concrete\Core\Database\CharacterSetCollation\Exception;

use Concrete\Core\Database\CharacterSetCollation\Exception;

class NoCharacterSetCollationDefinedException extends Exception
{
    public function __construct($message = null)
    {
        parent::__construct(t('Neither the character set nor the collation are defined.'));
    }
}
