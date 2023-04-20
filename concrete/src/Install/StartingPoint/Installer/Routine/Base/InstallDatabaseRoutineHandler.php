<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Messenger\Transport\DefaultAsync\DefaultAsyncConnection;
use Concrete\Core\Package\Package;
use Concrete\Core\Updater\Migrations\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class InstallDatabaseRoutineHandler
{

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Repository
     */
    protected $config;

    public function __construct(Connection $db, Application $app, Repository $config)
    {
        $this->db = $db;
        $this->app = $app;
        $this->config = $config;
    }

    protected function indexAdditionalDatabaseFields()
    {
        $textIndexes = $this->config->get('database.text_indexes');
        $this->db->createTextIndexes($textIndexes);
    }

    public function __invoke()
    {
        $num = $this->db->GetCol('show tables');

        if (count($num) > 0) {
            throw new \Exception(
                t(
                    'There are already %s tables in this database. Concrete must be installed in an empty database.',
                    count($num)));
        }
        $installDirectory = DIR_BASE_CORE . '/config';
        try {
            // Retrieving metadata from the entityManager created with \ORM::entityManager()
            // will result in a empty metadata array. Because all drivers are wrapped in a driverChain
            // the method getAllMetadata() of Doctrine\Common\Persistence\Mapping\AbstractClassMetadataFactory
            // is going to return a empty array. To overcome this issue a new EntityManager is create with the
            // only purpose to be used during the installation.
            $config = Setup::createConfiguration(true, $this->config->get('database.proxy_classes'));
            \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('subpackages');
            \Doctrine\Common\Annotations\AnnotationReader::addGlobalIgnoredName('package');
            // Use default AnnotationReader
            $driverImpl = $config->newDefaultAnnotationDriver(DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES, false);
            $config->setMetadataDriverImpl($driverImpl);
            $em = EntityManager::create($this->db, $config);
            $dbm = new DatabaseStructureManager($em);
            $dbm->destroyProxyClasses();
            $dbm->generateProxyClasses();

            Package::installDB($installDirectory . '/db.xml');

            $dbm->installDatabase();
            $this->indexAdditionalDatabaseFields();

            $configuration = new Configuration();
            $version = $configuration->getVersion($this->config->get('concrete.version_db'));
            $version->markMigrated();
            $configuration->registerPreviousMigratedVersions();
        } catch (\Exception $e) {
            throw new \Exception(t('Unable to install database: %s', $e->getMessage()));
        }
        $connection = $this->app->make(DefaultAsyncConnection::class)->getWrappedConnection();
        $connection->setup();
    }


}
