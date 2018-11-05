<?php
namespace Concrete\Core\Database\Schema;

use Doctrine\DBAL\Schema\MySqlSchemaManager as DoctrineMySqlSchemaManager;
use Doctrine\DBAL\Schema\Table;

class MySqlSchemaManager extends DoctrineMySqlSchemaManager
{
    /**
     * {@inheritdoc}
     *
     * @see \Doctrine\DBAL\Schema\AbstractSchemaManager::createTable()
     */
    public function createTable(Table $table)
    {
        if (!$table->hasOption('charset') && !$table->hasOption('collate')) {
            $connParams = $this->_conn->getParams();
            if (isset($connParams['defaultTableOptions']['charset']) && isset($connParams['defaultTableOptions']['collate'])) {
                $table
                    ->addOption('charset', $connParams['defaultTableOptions']['charset'])
                    ->addOption('collate', $connParams['defaultTableOptions']['collate'])
                ;
            }
        }

        return parent::createTable($table);
    }
}
