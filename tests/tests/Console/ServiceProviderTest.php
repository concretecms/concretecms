<?php

namespace Concrete\Tests\Controller;

use Concrete\Core\Application\Application;
use Concrete\Core\Console\Application as ConsoleApplication;
use Concrete\Core\Console\ServiceProvider;
use Concrete\Core\Database\Connection\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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

    public function testProviderExtends()
    {
        // Run the register function
        $this->provider->register();

        // Make sure that we have a valid callable after registering
        $this->assertTrue(is_callable($this->consoleFactory), 'The bound value isn\'t callable.');
    }

    /**
     * Make sure that the expected commands exist when concrete5 is not installed
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
            \Concrete\Core\Console\Command\InfoCommand::class,
            \Concrete\Core\Console\Command\InstallCommand::class,
            \Concrete\Core\Console\Command\InstallLanguageCommand::class,
            \Concrete\Core\Console\Command\TranslatePackageCommand::class,
            \Concrete\Core\Console\Command\GenerateIDESymbolsCommand::class,
            \Concrete\Core\Console\Command\ConfigCommand::class,
            \Concrete\Core\Console\Command\PackPackageCommand::class,
            \Concrete\Core\Console\Command\ExecCommand::class,
            \Concrete\Core\Console\Command\ServiceCommand::class,
            \Concrete\Core\Console\Command\ResetCommand::class,
        ];

        sort($subset);
        sort($this->addedClasses);

        // Check that the classes we expect have been added
        $this->assertArraySubset($subset, $this->addedClasses);
    }

    /**
     * Make sure expected commands exist when concrete5 is installed
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
            \Concrete\Core\Console\Command\InfoCommand::class,
            \Concrete\Core\Console\Command\InstallCommand::class,
            \Concrete\Core\Console\Command\InstallLanguageCommand::class,
            \Concrete\Core\Console\Command\TranslatePackageCommand::class,
            \Concrete\Core\Console\Command\GenerateIDESymbolsCommand::class,
            \Concrete\Core\Console\Command\ConfigCommand::class,
            \Concrete\Core\Console\Command\PackPackageCommand::class,
            \Concrete\Core\Console\Command\ExecCommand::class,
            \Concrete\Core\Console\Command\ServiceCommand::class,
            \Concrete\Core\Console\Command\ResetCommand::class,
            \Concrete\Core\Console\Command\CompareSchemaCommand::class,
            \Concrete\Core\Console\Command\ClearCacheCommand::class,
            \Concrete\Core\Console\Command\InstallPackageCommand::class,
            \Concrete\Core\Console\Command\UninstallPackageCommand::class,
            \Concrete\Core\Console\Command\UpdatePackageCommand::class,
            \Concrete\Core\Console\Command\BlacklistClear::class,
            \Concrete\Core\Console\Command\JobCommand::class,
            \Concrete\Core\Console\Command\UpdateCommand::class,
            \Concrete\Core\Console\Command\FillThumbnailsTableCommand::class,
            \Concrete\Core\Console\Command\GenerateSitemapCommand::class,
            \Concrete\Core\Console\Command\RefreshEntitiesCommand::class,
            \Doctrine\DBAL\Tools\Console\Command\ImportCommand::class,
            \Doctrine\DBAL\Tools\Console\Command\RunSqlCommand::class,
            \Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand::class,
            \Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand::class,
            \Doctrine\ORM\Tools\Console\Command\ClearCache\ResultCommand::class,
            \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand::class,
            \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand::class,
            \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand::class,
            \Doctrine\ORM\Tools\Console\Command\EnsureProductionSettingsCommand::class,
            \Doctrine\ORM\Tools\Console\Command\ConvertDoctrine1SchemaCommand::class,
            \Doctrine\ORM\Tools\Console\Command\GenerateRepositoriesCommand::class,
            \Doctrine\ORM\Tools\Console\Command\GenerateEntitiesCommand::class,
            \Doctrine\ORM\Tools\Console\Command\GenerateProxiesCommand::class,
            \Doctrine\ORM\Tools\Console\Command\ConvertMappingCommand::class,
            \Doctrine\ORM\Tools\Console\Command\RunDqlCommand::class,
            \Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand::class,
            \Doctrine\ORM\Tools\Console\Command\InfoCommand::class,
            \Doctrine\ORM\Tools\Console\Command\MappingDescribeCommand::class,
            \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand::class,
            \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand::class,
            \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand::class,
            \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand::class,
            \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand::class,
            \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand::class
        ];

        sort($subset);
        sort($this->addedClasses);

        $this->assertArraySubset($subset, $this->addedClasses);
    }

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
        $app->shouldReceive('extend')->zeroOrMoreTimes()->andReturnUsing(function ($class, callable $binding) {
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
}
