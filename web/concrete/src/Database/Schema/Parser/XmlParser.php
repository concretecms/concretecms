<?php
namespace Concrete\Core\Database\Schema\Parser;

abstract class XmlParser
{

    protected $rawXML;
    protected $ignoreExistingTables = true;

    public function __construct(\SimpleXMLElement $xml)
    {
        $this->rawXML = $xml;
    }

    public function setIgnoreExistingTables($ignoreExistingTables)
    {
        $this->ignoreExistingTables = $ignoreExistingTables;
    }

    abstract public function parse(\Concrete\Core\Database\Connection\Connection $db);

}
