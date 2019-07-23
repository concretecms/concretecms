<?php

namespace Concrete\Core\Database\Platforms\Keywords;

use Doctrine\DBAL\Platforms\Keywords\MySQL57Keywords;

/**
 * Backport of https://github.com/doctrine/dbal/pull/3128.
 */
class MySQL80Keywords extends MySQL57Keywords
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Platforms\Keywords\MySQL57Keywords::getName()
     */
    public function getName()
    {
        return 'MySQL80';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Platforms\Keywords\MySQL57Keywords::getKeywords()
     */
    protected function getKeywords()
    {
        $keywords = parent::getKeywords();

        $keywords = array_merge($keywords, [
            'ADMIN',
            'CUBE',
            'CUME_DIST',
            'DENSE_RANK',
            'EMPTY',
            'EXCEPT',
            'FIRST_VALUE',
            'FUNCTION',
            'GROUPING',
            'GROUPS',
            'JSON_TABLE',
            'LAG',
            'LAST_VALUE',
            'LEAD',
            'NTH_VALUE',
            'NTILE',
            'OF',
            'OVER',
            'PERCENT_RANK',
            'PERSIST',
            'PERSIST_ONLY',
            'RANK',
            'RECURSIVE',
            'ROW',
            'ROWS',
            'ROW_NUMBER',
            'SYSTEM',
            'WINDOW',
        ]);

        return $keywords;
    }
}
