<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Block\BlockType\BlockTypeList;
use Concrete\Core\Console\Command;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Database\Schema\Schema;
use Concrete\Core\Foundation\Environment;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Package;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompareSchemaCommand extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('c5:compare-schema')
            ->setDescription('Compares db.xml in concrete5 XML schema, concrete5 entities, and all installed package schemas and entities with the contents of the database and prints the difference.')
            ->addEnvOption()
            ->setHelp(<<<EOT
Returns codes:
  0 operation completed successfully
  $errExitCode errors occurred
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $db = \Database::connection();
        $em = $db->getEntityManager();
        $cacheDriver = $em->getConfiguration()->getMetadataCacheImpl();
        $cacheDriver->flushAll();

        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $schemas = [];
        $sm = $db->getSchemaManager();
        $dbSchema = $sm->createSchema();

        // core xml tables
        $schemas[] = Schema::getCoreXMLSchema();

        // core entities
        $sm = new DatabaseStructureManager($em);
        $entities = $sm->getMetadatas();
        $schemas[] = $tool->getSchemaFromMetadata($entities);

        // core, application and package block types
        $env = Environment::get();
        $list = new BlockTypeList();
        $list->includeInternalBlockTypes();
        foreach ($list->get() as $bt) {
            $r = $env->getRecord(DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . FILENAME_BLOCK_DB, $bt->getPackageHandle());
            if ($r->exists()) {
                $parser = Schema::getSchemaParser(simplexml_load_file($r->file));
                $parser->setIgnoreExistingTables(false);
                $schemas[] = $parser->parse($db);
            }
        }

        // packages
        $packages = Package::getInstalledList();
        foreach ($packages as $pkg) {
            $xmlFile = $pkg->getPackagePath() . '/' . FILENAME_BLOCK_DB;
            if (file_exists($xmlFile)) {
                $parser = Schema::getSchemaParser(simplexml_load_file($xmlFile));
                $parser->setIgnoreExistingTables(false);
                $schemas[] = $parser->parse($db);
            }
        }

        // Finalize output.
        $comparator = new \Doctrine\DBAL\Schema\Comparator();
        $saveQueries = [];
        foreach ($schemas as $schema) {
            $schemaDiff = $comparator->compare($dbSchema, $schema);
            $saveQueries = array_merge($saveQueries, $schemaDiff->toSaveSql($db->getDatabasePlatform()));
        }

        $saveQueries = $this->filterQueries($saveQueries);
        if (count($saveQueries)) {
            $output->writeln(t2('%s query found', '%s queries found', count($saveQueries)));
            $i = 1;
            foreach ($saveQueries as $query) {
                $output->writeln(sprintf('%s: %s', $i, $query));
                ++$i;
            }
        } else {
            $output->writeln(t('No differences found between schema and database.'));
        }
    }

    /**
     * Filter out all the queries that are platform specific that
     * Doctrine doens't give us a good way to deal with. This is mostly
     * index lengths that are set in installation that Doctrine doesn't
     * support.
     *
     * @param string[] $queries
     *
     * @return string[]
     */
    protected function filterQueries($queries)
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');

        $textIndexDrops = [];
        foreach ($config->get('database.text_indexes') as $indexTable => $indexDefinition) {
            foreach (array_keys($indexDefinition) as $indexName) {
                $textIndexDrops[] = strtolower("DROP INDEX {$indexName} ON {$indexTable}");
            }
        }

        $returnQueries = [];
        foreach ($queries as $query) {
            $queryLowerCase = strtolower($query);
            if (!in_array($queryLowerCase, $textIndexDrops, true)) {
                $returnQueries[] = $query;
            }
        }

        return $returnQueries;
    }
}
