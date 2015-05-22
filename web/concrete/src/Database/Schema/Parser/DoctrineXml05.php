<?php

namespace Concrete\Core\Database\Schema\Parser;

class DoctrineXml05 extends XmlParser
{
    /**
     * Transforms the XML from Adodb XML into
     * Doctrine DBAL Schema.
     */
    public function parse(\Concrete\Core\Database\Connection\Connection $db)
    {
        $filter = null;
        if ($this->ignoreExistingTables) {
            $filter = function ($tableName) use ($db) {
              return $db->tableExists($tableName) ? false : true;
            };
        }

        return \DoctrineXml\Parser::fromDocument(
            $this->rawXML->asXML(),
            $db->getDatabasePlatform(),
            true,
            false,
            $filter
        );
    }
}
