<?php

namespace Concrete\Tests\Controller;

use Concrete\Core\Application\Application;
use Concrete\Core\Console\Application as ConsoleApplication;
use Concrete\Core\Console\ServiceProvider;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Console\Command as ConcreteCommand;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Tools\Console\Command as DBALCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Command as DBALMigrationCommand;
use Doctrine\ORM\Tools\Console\Command as ORMCommand;
use Mockery;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Command\Command;

class ServiceProviderTest extends PHPUnit_Framework_TestCase
{
    /** @var \Mockery\Mock|Application */
    protected $app;

    /** @var ServiceProvider */
    protected $provider;

    /** @var callable function(ConsoleApplication $app): ConsoleApplication */
    protected $consoleFactory;

    /** @var ConsoleApplication */
    protected $console;

    /** @var array The tracked command classes that get added */
    protected $addedClasses = [];

    public function setUp()
    {
        // Setup a fake app object
        $app = Mockery::mock(Application::class)->makePartial();

        // Setup a fake database connection
        $app->bind(EntityManagerInterface::class, function () {
            $connection = Mockery::mock(Connection::class);
            $em = Mockery::mock(EntityManager::class)->makePartial();
            $em->shouldReceive('getConnection')->zeroOrMoreTimes()->andReturn($connection);

            return $em;
        });

        // Setup the extend method
        $app->shouldReceive('extend')->with(ConsoleApplication::class, Mockery::type('callable'))->zeroOrMoreTimes()->andReturnUsing(function ($class, callable $binding) {
            $this->consoleFactory = $binding;
        });

        // Setup a console object
        $this->console = Mockery::mock(ConsoleApplication::class)->makePartial();
        $this->console->shouldReceive('add')->andReturnUsing(function (Command $command) {
            $this->addedClasses[] = get_class($command);
        });

        // Setup the provider
        $this->provider = new ServiceProvider($app);
        $this->app = $app;
    }

    public function tearDown()
    {
        Mockery::close();
        $this->app = null;
        $this->provider = null;
        $this->consoleFactory = null;
        $this->addedClasses = [];
    }

    public function testProviderExtends()
    {
        // Run the register function
        $this->provider->register();

        // Make sure that we have a valid callable after registering
        $this->assertTrue(is_callable($this->consoleFactory), 'The bound value isn\'t callable.');
    }

    /**
     * Make sure that the expected commands exist when concrete5 is not installed.
     */
    public function testUninstalledHasExpectedCommands()
    {
        $this->app->shouldReceive('isInstalled')->andReturn(false);

        // Run the provider registration
        $this->provider->register();

        // Pass the mock through the factory
        $factory = $this->consoleFactory;
        $factory($this->console);

        $subset = [
            ConcreteCommand\InfoCommand::class,
            ConcreteCommand\InstallCommand::class,
            ConcreteCommand\InstallLanguageCommand::class,
            ConcreteCommand\TranslatePackageCommand::class,
            ConcreteCommand\GenerateIDESymbolsCommand::class,
            ConcreteCommand\ConfigCommand::class,
            ConcreteCommand\PackPackageCommand::class,
            ConcreteCommand\ExecCommand::class,
            ConcreteCommand\ServiceCommand::class,
            ConcreteCommand\ResetCommand::class,
            ConcreteCommand\PhpCodingStyleCommand::class,
        ];

        sort($subset);
        sort($this->addedClasses);

        // Check that the classes we expect have been added
        $this->assertArraySubset($subset, $this->addedClasses);
    }

    /**
     * Make sure expected commands exist when concrete5 is installed.
     */
    public function testInstalledHasExpectedCommands()
    {
        $this->app->shouldReceive('isInstalled')->andReturn(true);

        // Run the provider registration
        $this->provider->register();

        // Pass the mock through the factory
        $factory = $this->consoleFactory;
        $factory($this->console);

        // Check that the classes we expect have been added
        $subset = [
            ConcreteCommand\InfoCommand::class,
            ConcreteCommand\InstallCommand::class,
            ConcreteCommand\InstallLanguageCommand::class,
            ConcreteCommand\TranslatePackageCommand::class,
            ConcreteCommand\GenerateIDESymbolsCommand::class,
            ConcreteCommand\ConfigCommand::class,
            ConcreteCommand\PackPackageCommand::class,
            ConcreteCommand\ExecCommand::class,
            ConcreteCommand\ServiceCommand::class,
            ConcreteCommand\ResetCommand::class,
            ConcreteCommand\PhpCodingStyleCommand::class,
            ConcreteCommand\CompareSchemaCommand::class,
            ConcreteCommand\ClearCacheCommand::class,
            ConcreteCommand\InstallPackageCommand::class,
            ConcreteCommand\UninstallPackageCommand::class,
            ConcreteCommand\UpdatePackageCommand::class,
            ConcreteCommand\InstallThemeCommand::class,
            ConcreteCommand\BlacklistClear::class,
            ConcreteCommand\SetDatabaseCharacterSetCommand::class,
            ConcreteCommand\JobCommand::class,
            ConcreteCommand\UpdateCommand::class,
            ConcreteCommand\RescanFilesCommand::class,
            ConcreteCommand\FillThumbnailsTableCommand::class,
            ConcreteCommand\GenerateSitemapCommand::class,
            ConcreteCommand\RefreshEntitiesCommand::class,
            ConcreteCommand\Express\ExportCommand::class,
            ConcreteCommand\FixDatabaseForeignKeys::class,
            DBALCommand\ImportCommand::class,
            DBALCommand\RunSqlCommand::class,
            ORMCommand\ClearCache\MetadataCommand::class,
            ORMCommand\ClearCache\QueryCommand::class,
            ORMCommand\ClearCache\ResultCommand::class,
            ORMCommand\SchemaTool\CreateCommand::class,
            ORMCommand\SchemaTool\UpdateCommand::class,
            ORMCommand\SchemaTool\DropCommand::class,
            ORMCommand\EnsureProductionSettingsCommand::class,
            ORMCommand\ConvertDoctrine1SchemaCommand::class,
            ORMCommand\GenerateRepositoriesCommand::class,
            ORMCommand\GenerateEntitiesCommand::class,
            ORMCommand\GenerateProxiesCommand::class,
            ORMCommand\ConvertMappingCommand::class,
            ORMCommand\RunDqlCommand::class,
            ORMCommand\ValidateSchemaCommand::class,
            ORMCommand\InfoCommand::class,
            ORMCommand\MappingDescribeCommand::class,
            DBALMigrationCommand\DiffCommand::class,
            DBALMigrationCommand\ExecuteCommand::class,
            DBALMigrationCommand\GenerateCommand::class,
            DBALMigrationCommand\MigrateCommand::class,
            DBALMigrationCommand\StatusCommand::class,
            DBALMigrationCommand\VersionCommand::class,
        ];

        sort($subset);
        sort($this->addedClasses);

        $added = $this->addedClasses;
        $discrepency = implode(', ', array_diff($this->addedClasses, array_replace_recursive($added, $subset)));

        $this->assertEquals('', $discrepency, 'Command subset doesn\'t match.');
    }
}
