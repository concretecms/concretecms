<?php
namespace Concrete\Core\Database\Schema;

class Schema
{

    public static function loadFromXMLFile($file, \Concrete\Core\Database\Connection\Connection $connection)
    {
        $sx = simplexml_load_file($file);
        return static::loadFromXMLElement($sx, $connection);
    }

    public static function loadFromXMLElement(
        \SimpleXMLElement $sx,
        \Concrete\Core\Database\Connection\Connection $connection
    ) {
        $parser = static::getSchemaParser($sx);
        return $parser->parse($connection);
    }

    public static function loadFromArray($array, \Concrete\Core\Database\Connection\Connection $connection)
    {
        $parser = new \Concrete\Core\Database\Schema\Parser\ArrayParser();
        return $parser->parse($array, $connection);
    }

    public static function getSchemaParser(\SimpleXMLElement $sx)
    {
        $sx->registerXPathNamespace('dx0.5', 'http://www.concrete5.org/doctrine-xml/0.5');
        if ($sx->xpath('/dx0.5:schema')) {
            $parser = new \Concrete\Core\Database\Schema\Parser\DoctrineXml05($sx);
        } else {
            switch ($sx['version']) {
                case '0.3':
                    $parser = new \Concrete\Core\Database\Schema\Parser\Axmls($sx);
                    break;
                default:
                    throw new \Exception(t('Invalid schema version found. Expecting 0.3'));
            }
        }
        return $parser;
    }

    public static function refreshCoreXMLSchema($tables)
    {

        $xml = simplexml_load_file(DIR_BASE_CORE . '/config/db.xml');
        $output = new \SimpleXMLElement('<schema xmlns="http://www.concrete5.org/doctrine-xml/0.5" />');
        $th = \Core::make('helper/text');
        foreach($xml->table as $t) {
            $name = (string) $t['name'];
            if (in_array($name, $tables)) {
                $th->appendXML($output, $t);
            }
        }

        $db = \Database::get();

        $parser = static::getSchemaParser($output);
        $parser->setIgnoreExistingTables(false);
        $toSchema = $parser->parse($db);
        $fromSchema = $db->getSchemaManager()->createSchema();
        $comparator = new \Doctrine\DBAL\Schema\Comparator();
        $schemaDiff = $comparator->compare($fromSchema, $toSchema);
        $saveQueries = $schemaDiff->toSaveSql($db->getDatabasePlatform());

        foreach($saveQueries as $query) {
            $db->query($query);
        }
    }
}
