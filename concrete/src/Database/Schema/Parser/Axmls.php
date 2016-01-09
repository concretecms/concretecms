<?php
namespace Concrete\Core\Database\Schema\Parser;

class Axmls extends XmlParser
{

    /**
     * Transforms the XML from Adodb XML into
     * Doctrine DBAL Schema
     */
    public function parse(\Concrete\Core\Database\Connection\Connection $db)
    {
        $x = $this->rawXML;
        $schema = new \Doctrine\DBAL\Schema\Schema();
        foreach ($x->table as $t) {

            if ($this->ignoreExistingTables && $db->tableExists($t['name'])) {
                continue;
            }
            $table = $schema->createTable((string)$t['name']);
            foreach ($t->field as $f) {
                $options = $this->_getColumnOptions($db, $f);
                $version = (isset($options['version']) && $options['version']) ? true : false;
                unset($options['version']);
                $field = $table->addColumn((string)$f['name'], $this->_getColumnType($f), $options);
                if ($version) {
                    $field->setPlatformOption('version', true);
                }
            }
            $this->_setPrimaryKeys($db, $t, $table);
            $this->_setIndexes($db, $t, $table);
            $this->_setTableOpts($db, $t, $table);
        }
        return $schema;
    }

    protected function _setTableOpts(
        \Concrete\Core\Database\Connection\Connection $db,
        \SimpleXMLElement $table,
        $schemaTable
    ) {
        if ($table->opt) {
            $opt = $table->opt->__toString();
            if ($opt == 'ENGINE=MYISAM') {
                $schemaTable->addOption('engine', 'MYISAM');
            }
        }
    }

    protected function _setPrimaryKeys(
        \Concrete\Core\Database\Connection\Connection $db,
        \SimpleXMLElement $table,
        $schemaTable
    ) {
        $primaryKeys = array();
        foreach ($table->field as $column) {
            if ($column->autoincrement || $column->AUTOINCREMENT || $column->key || $column->KEY) {
                $primaryKeys[] = (string)$column['name'];
            }
        }
        if (count($primaryKeys) > 0) {
            $schemaTable->setPrimaryKey($primaryKeys);
        }
    }

    protected function _setIndexes(
        \Concrete\Core\Database\Connection\Connection $db,
        \SimpleXMLElement $table,
        $schemaTable
    ) {
        foreach ($table->index as $index) {
            $name = (string)$index['name'];
            $fields = array();
            $flags = array();
            foreach ($index->col as $col) {
                $fields[] = $col->__toString();
            }
            if ($index->fulltext || $index->FULLTEXT) {
                $flags[] = 'FULLTEXT';
            }
            if ($index->UNIQUE || $index->unique) {
                $schemaTable->addUniqueIndex($fields, $name);
            } else {
                $schemaTable->addIndex($fields, $name, $flags);
            }
        }
    }

    protected function _getColumnOptions(\Concrete\Core\Database\Connection\Connection $db, \SimpleXMLElement $column)
    {
        $type = strtoupper((string)$column['type']);
        $size = (string)$column['size'];
        $options = array();
        if ($size) {
            if (in_array($type, array('N', 'F'))) {
                $precision = explode('.', $size);
                $options['precision'] = $precision[0];
                $options['scale'] = $precision[1];
            } else {
                $options['length'] = $size;
            }
        }
        switch ($type) {
            case 'X':
                $options['length'] = 65535; // this means 'X' will result in a 'TEXT' column
                break;
            case 'X2':
                // no length limitation -> this means 'X2' will result in a 'LONGTEXT' column
                break;
        }
        if ($column->unsigned || $column->UNSIGNED) {
            $options['unsigned'] = true;
        }
        if ($column->default) {
            if (isset($column->default['value'])) {
                $options['default'] = (string)$column->default['value'];
            }
            if (isset($column->default['VALUE'])) {
                $options['default'] = (string)$column->default['VALUE'];
            }
        }
        if ($column->DEFAULT) {
            if (isset($column->DEFAULT['value'])) {
                $options['default'] = (string)$column->DEFAULT['value'];
            }
            if (isset($column->DEFAULT['VALUE'])) {
                $options['default'] = (string)$column->DEFAULT['VALUE'];
            }
        }
        if ($column->notnull || $column->NOTNULL) {
            $options['notnull'] = true;
        } else {
            $options['notnull'] = false;
        }
        if ($column->autoincrement || $column->AUTOINCREMENT) {
            $options['autoincrement'] = true;
        }
        if ($type == 'T' && isset($column->deftimestamp) || isset($column->DEFTIMESTAMP)) {
            $platform = $db->getDatabasePlatform();
            $options['default'] = $platform->getCurrentTimestampSQL();
            $options['version'] = true;
        }
        return $options;
    }

    protected function _getColumnType(\SimpleXMLElement $column)
    {
        $type = strtoupper((string)$column['type']);
        $size = (string)$column['size'];
        if ($type == 'L') {
            return 'boolean';
        }
        if ($type == 'I1') {
            if ($size != '' && $size > 1) {
                return 'smallint';
            } else {
                return 'boolean';
            }
        }
        if ($type == 'I2') {
            return 'smallint';
        }
        if ($type == 'I4') {
            return 'integer';
        }
        if ($type == 'I') {
            if ($size === '1') {
                return 'boolean';
            }
            if ($size != '' && $size < 5) {
                return 'smallint';
            }
            return 'integer';
        }
        if ($type == 'I8') {
            return 'bigint';
        }
        if ($type == 'C') {
            return 'string';
        }
        if ($type == 'F') {
            return 'float';
        }
        if ($type == 'X') {
            return 'text';
        }
        if ($type == 'XL') {
            return 'text';
        }
        if ($type == 'C2') {
            return 'string';
        }
        if ($type == 'X2') {
            return 'text';
        }
        if ($type == 'T') {
            return 'datetime';
        }
        if ($type == 'TS') {
            return 'datetime';
        }
        if ($type == 'D') {
            return 'date';
        }
        if ($type == 'N') {
            return 'decimal';
        }
        if ($type == 'B') {
            return 'blob';
        }

        // This is not strict AXMLS but it will be useful for those who want to use
        // certain Doctrine type features that AXMLS doesn't support
        if ($type == 'TIME') {
            return 'time';
        }
    }

}

