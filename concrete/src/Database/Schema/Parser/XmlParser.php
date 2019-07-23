<?php

namespace Concrete\Core\Database\Schema\Parser;

use Concrete\Core\Database\Connection\Connection;
use Doctrine\DBAL\Driver\ServerInfoAwareConnection;
use Doctrine\DBAL\VersionAwarePlatformDriver;
use SimpleXMLElement;

abstract class XmlParser
{
    /**
     * @var \SimpleXMLElement
     */
    protected $rawXML;

    /**
     * @var bool
     */
    protected $ignoreExistingTables = true;

    public function __construct(SimpleXMLElement $xml)
    {
        $this->rawXML = $xml;
    }

    /**
     * @param bool $ignoreExistingTables
     */
    public function setIgnoreExistingTables($ignoreExistingTables)
    {
        $this->ignoreExistingTables = $ignoreExistingTables;
    }

    /**
     * @param \Concrete\Core\Database\Connection\Connection $db
     */
    abstract public function parse(Connection $db);

    /**
     * @param \Concrete\Core\Database\Connection\Connection $db
     *
     * @return string|null
     */
    protected function getDatabaseVersion(Connection $db)
    {
        $driver = $db->getDriver();
        if (!$driver instanceof VersionAwarePlatformDriver) {
            return null;
        }
        $params = $db->getParams();
        if (isset($params['serverVersion'])) {
            $rawVersion = (string) $params['serverVersion'];
        } else {
            $connection = $db->getWrappedConnection();
            if (!$connection instanceof ServerInfoAwareConnection) {
                return null;
            }
            $rawVersion = (string) $connection->getServerVersion();
        }
        if (!preg_match('/^\d+\.\d+(?:\.\d+)?/', $rawVersion, $matches)) {
            return null;
        }

        return $matches[0];
    }
}
