<?php

namespace Concrete\Core\Database\Platforms;

use Concrete\Core\Database\Platforms\Keywords\MySQL80Keywords;
use Doctrine\DBAL\Platforms\MySQL57Platform;

/**
 * Backport of https://github.com/doctrine/dbal/pull/3128.
 */
class MySQL80Platform extends MySQL57Platform
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Platforms\MySQL57Platform::getReservedKeywordsClass()
     */
    protected function getReservedKeywordsClass()
    {
        return MySQL80Keywords::class;
    }
}
