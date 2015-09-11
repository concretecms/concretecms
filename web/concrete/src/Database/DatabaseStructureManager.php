<?php
namespace Concrete\Core\Database;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\ORM\EntityManager;
use Core;

class DatabaseStructureManager
{

    /**
     * The entity manager instance.
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * The entity classes and their metadata.
     * 
     * @var \Doctrine\Common\Persistence\Mapping\ClassMetadata[]
     */
    protected $metadatas;

    /**
     * Create a new structure manager.
     * 
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    /**
     * Get the entity manager object for this structure manager.
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * Returns the proxies path.
     * 
     * @return string
     */
    public function getProxyDir()
    {
        return $this->getEntityManager()->getConfiguration()->getProxyDir();
    }

    /**
     * Generates the proxy classes for all the entities managed by this class.
     * Returns true on successful generation and false if there were no proxy
     * classes to be generated.
     * 
     * @return boolean
     */
    public function generateProxyClasses()
    {
        $metadatas = $this->getMetadatas();
        return $this->generateProxyClassesFor($metadatas);
    }

    /**
     * Generates the proxy classes for all given classes contained in the
     * $metadatas array. Returns true on successful generation and false if
     * there were no proxy classes to be generated
     * 
     * @param  array $metadatas
     * @return boolean
     * @throws \Exception Throws an exception in case there are issues with the proxy dir.
     */
    public function generateProxyClassesFor(array $metadatas)
    {
        if (count($metadatas) > 0) {
            // First create the proxies directory if it does not already exist.
            $proxyDir = $this->getProxyDir();
            if (!is_dir($proxyDir)) {
                if (file_exists($proxyDir)) {
                    throw new \Exception(t(
                        "A file exists in place of the proxy directory. " .
                        "Please remove the '%s' file to proceed with the " . 
                        "proxy class generation.",
                        $proxyDir
                    ));
                }
                @mkdir($proxyDir, DIRECTORY_PERMISSIONS_MODE_COMPUTED, true);
                if (is_dir($proxyDir)) {
                    @chmod($proxyDir, DIRECTORY_PERMISSIONS_MODE_COMPUTED);
                } else {
                    throw new \Exception(t(
                        "Could not create the proxies directory. " .
                        "Please check the file permissions of the proxy " . 
                        "directory: %s.",
                        $proxyDir
                    ));
                }
            }

            // Generate the proxy classes
            $pf = $this->getEntityManager()->getProxyFactory();
            $pf->generateProxyClasses($metadatas);

            return true;
        }
        return false;
    }

    /**
     * Destroys all the proxy classes that have the defined prefix. No need to
     * define the generic doctrine proxy marker prefix, i.e. "__CG__" but the
     * part after that, e.g. "ConcreteCore".
     * 
     * Returns a boolean indicating whether any files were deleted or not.
     * 
     * @param  string $prefix
     * @return boolean
     * @throws \Exception Throws an exception if the given prefix is invalid or 
     *         if one of the proxy files cannot be deleted.
     */
    public function destroyProxyClasses($prefix)
    {
        if (!is_string($prefix) || strlen($prefix) < 1) {
            throw new \Exception(t("The given prefix needs to be a string."));
        }
        $proxyDir = $this->getProxyDir();
        if (is_dir($proxyDir)) {
            $fh = Core::make('helper/file');
            $prefix = \Doctrine\Common\Proxy\Proxy::MARKER . $prefix;
            $filesMatched = 0;
            foreach ($fh->getDirectoryContents($proxyDir) as $file) {
                if (strpos($file, $prefix) === 0) {
                    if (!@unlink($proxyDir . '/' . $file)) {
                        throw new \Exception(t(
                            "Could not delete a proxy file. Please check the " .
                            "permissions of the proxy directory: %s",
                            $proxyDir
                        ));
                    }
                    $filesMatched++;
                }
            }
            return $filesMatched > 0;
        }
        return false;
    }

    /** 
     * Installs the database tables according to the entity schema definitions.
     * This will not install any existing tables but it will migrate those
     * tables to match the current schema definitions for the classes.
     * 
     * @return boolean
     */
    public function installDatabase()
    {
        $metadatas = $this->getMetadatas();
        return $this->installDatabaseFor($metadatas);
    }

    /**
     * Installs the database tables for all entity classes contained within the
     * $metadatas array. Returns true if new tables were created or existing
     * ones altered. Otherwise this will return false if there were no database
     * migrations needed.
     * 
     * @param  array $metadatas
     * @return boolean
     */
    public function installDatabaseFor(array $metadatas)
    {
        if (count($metadatas) > 0) {
            // We need to create the SchemaDiff manually here because we want
            // to avoid calling the execution for two separate SchemaDiff
            // objects (one for missing tables and one for new ones).
            // Also, while $tool->createSchema($missingEntities) works great
            // for new tables, $tool->updateSchema($updateEntities) would
            // actually delete all the DB tables that the DB contains and are
            // not part of the entity tables passed to the function. Therefore,
            // we do this manually here.
            $em = $this->getEntityManager();
            $conn = $em->getConnection();
            $sm = $conn->getSchemaManager();
            $cmf = $em->getMetadataFactory();
            $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
            $comparator = new \Doctrine\DBAL\Schema\Comparator();

            // NOTE: $newSchema != $toSchema because $toSchema would actually
            // contain each and every table in the database. We'll only need
            // to traverse the $newSchema for the purposes of the desired
            // functionality but we also need $fromSchema to check whether
            // the table already exists and also to get the current schema
            // for that table to figure out the changes to the new table.
            $fromSchema = $sm->createSchema();
            $newSchema = $tool->getSchemaFromMetadata($metadatas);
            $newTables = array();
            $changedTables = array();
            foreach ($newSchema->getTables() as $newTable) {
                // Check if the table already exists
                if ($fromSchema->hasTable($newTable->getName())) {
                    $diff = $comparator->diffTable($fromSchema->getTable($newTable->getName()), $newTable);
                    if ($diff) {
                        $changedTables[] = $diff;
                    }
                } else {
                    $newTables[] = $newTable;
                }
            }
            if (count($newTables) > 0 || count($changedTables) > 0) {
                // If we have new or changed tables (or both), we'll gather
                // these DB changes into a SchemaDiff object and get all the
                // necessary DB migration queries for that diff object.
                // Finally, those queries are executed against the DB.
                $schemaDiff = new SchemaDiff($newTables, $changedTables);
                $platform = $conn->getDatabasePlatform();
                $migrateSql = $schemaDiff->toSql($platform);
                foreach ($migrateSql as $sql) {
                    $conn->executeQuery($sql);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * This drops all the tables related to the entities managed by this class.
     * 
     * Do not normally call this for anything. Save this ONLY for special
     * occasions.
     * 
     * @return boolean
     */
    public function uninstallDatabase()
    {
        $metadatas = $this->getMetadatas();
        return $this->uninstallDatabaseFor($metadatas);
    }

    /**
     * Uninstalls the database tables for all given entity classes contained
     * within the $metadatas array. Returns true if there were tables that were
     * dropped and false otherwise.
     * 
     * @param  array $metadatas
     * @return boolean
     */
    public function uninstallDatabaseFor(array $metadatas)
    {
        if (count($metadatas) > 0) {
            $em = $this->getEntityManager();
            $conn = $em->getConnection();
            $sm = $conn->getSchemaManager();
            $cmf = $em->getMetadataFactory();
            $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

            $newSchema = $tool->getSchemaFromMetadata($metadatas);

            // We'll let Doctrine resolve the correct drop order for the tables
            // because of which we use the schema migration method to drop the
            // tables. By letting Doctrine resolve the drop order we avoid
            // DB server constraint violation errors (e.g. in MySQL).
            $fromSchema = $sm->createSchema();
            $toSchema = clone $fromSchema;
            foreach ($newSchema->getTables() as $newTable) {
                if ($toSchema->hasTable($newTable->getName())) {
                    $toSchema->dropTable($newTable->getName());
                }
            }
            $sqls = $fromSchema->getMigrateToSql($toSchema, $conn->getDatabasePlatform());

            if (count($sqls) > 0) {
                foreach ($sqls as $sql) {
                    $conn->executeQuery($sql);
                }

                return true;
            }
        }
        return false;
    }

    /**
     * Drops all the database tables that
     * a) are prefixed with the given prefix string
     * b) are not linked to any existing entity managed by this class
     * c) are not contained within the $excludeTables array
     * 
     * Can be used e.g. for packages by giving the package's handle in
     * camelcased format. This would drop all the prefixed database tables
     * for that package that no longer have a corresponding entity defined
     * for them. Give the tables defined in the package's DB XML in the
     * $excludeTables array in order not to drop them.
     * 
     * @param  string $prefix
     * @param  array $excludeTables
     * @return int
     */
    public function dropObsoleteDatabaseTables($prefix, array $excludeTables = array())
    {
        $em = $this->getEntityManager();
        $conn = $em->getConnection();
        $sm = $conn->getSchemaManager();
        $cmf = $em->getMetadataFactory();
        // Exclude existing entity tables from being dropped
        $metadatas = $this->getMetadatas();
        foreach ($metadatas as $md) {
            $excludeTables[] = $md->getTableName();
        }
        $fromSchema = $sm->createSchema();
        $toSchema = clone $fromSchema;
        foreach ($fromSchema->getTables() as $tbl) {
            if (strpos($tbl->getName(), $prefix) === 0 && !in_array($tbl->getName(), $excludeTables)) {
                $toSchema->dropTable($tbl->getName());
            }
        }
        $sqls = $fromSchema->getMigrateToSql($toSchema, $conn->getDatabasePlatform());
        foreach ($sqls as $sql) {
            $conn->executeQuery($sql);
        }
    }

    /**
     * Determines whether the entity manager instance for this class has any
     * entity classes defined in its entity class path.
     * 
     * @return boolean
     */
    public function hasEntities()
    {
        return count($this->getMetadatas()) > 0;
    }

    /**
     * Returns the entity classes and their metadata. Loads this data if it has
     * not been already loaded by this instancfe.
     * 
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata[]
     */
    public function getMetadatas()
    {
        if (!isset($this->metadatas)) {
            $this->loadMetadatas();
        }
        return $this->metadatas;
    }

    /**
     * Loads the entity class metadata into the $metadatas variable.
     * 
     * @return void
     */
    protected function loadMetadatas()
    {
        try {
            $this->metadatas = array();
            $em = $this->getEntityManager();
            $cmf = $em->getMetadataFactory();
            foreach ($cmf->getAllMetadata() as $metaData) {
                $this->metadatas[$metaData->getName()] = $metaData;
            }
        } catch (MappingException $e) {
            // we don't want them complaining about a src directory not being in the package.
        }
    }

}
