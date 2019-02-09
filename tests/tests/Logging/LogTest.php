<?php

namespace Concrete\Tests\Logging;

use Cascade\Cascade;
use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\Configuration\AdvancedConfiguration;
use Concrete\Core\Logging\Configuration\ConfigurationFactory;
use Concrete\Core\Logging\Configuration\SimpleConfiguration;
use Concrete\Core\Logging\Configuration\SimpleDatabaseConfiguration;
use Concrete\Core\Logging\Configuration\SimpleFileConfiguration;
use Concrete\Core\Logging\GroupLogger;
use Concrete\Core\Logging\Handler\DatabaseHandler;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Logging\Processor\Concrete5UserProcessor;
use Concrete\Core\Site\Service;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Support\Facade\Log;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Illuminate\Filesystem\Filesystem;
use Mockery as M;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Concrete\Core\Logging\LogEntry;
use Monolog\Processor\PsrLogMessageProcessor;

class LogTest extends ConcreteDatabaseTestCase
{
    protected $fixtures = [];
    protected $tables = ['Logs'];
    protected $app;

    public function setUp()
    {
        parent::setUp();

        // Clear log every time
        $this->truncateTables();

        $this->app = Facade::getFacadeApplication();
        $this->db = $this->app->make(Connection::class);
    }

    // The application logger is used whenever the \Log facade is used. Consequently, the default behavior is to
    // always ensure that it logs everything to the database.
    public function testCreateApplicationLogger()
    {
        $factory = $this->app->make(LoggerFactory::class);
        $applicationLogger = $factory->createLogger(Channels::CHANNEL_APPLICATION);
        $this->assertInstanceOf(Logger::class, $applicationLogger);
        $processors = $applicationLogger->getProcessors();
        $handlers = $applicationLogger->getHandlers();
        $this->assertCount(1, $handlers);
        $this->assertCount(2, $processors);
        $this->assertInstanceOf(DatabaseHandler::class, $handlers[0]);
        $this->assertEquals(Logger::DEBUG, $handlers[0]->getLevel());

        $applicationLogger->debug('This is a debug line.');
        $applicationLogger->emergency('This is an emergency!');
        $applicationLogger->info('This is an info line.');

        $r = $this->db->GetAll('select * from Logs');
        $this->assertEquals('application', $r[0]['channel']);
        $this->assertEquals('This is a debug line.', $r[0]['message']);
        $this->assertCount(3, $r);
    }

    // This is a custom logger - by default it too should log everything because we want to be permissive like that.
    public function testCreateCustomLogger()
    {
        $factory = $this->app->make(LoggerFactory::class);
        $logger = $factory->createLogger('uploads');
        $logger->info('File testing.jpg uploaded.');

        $r = $this->db->GetAll('select * from Logs');
        $this->assertCount(1, $r);
    }

    // Now we get to the interesting new stuff. Let's log some core stuff and see if it logs. Since this core stuff
    // are notices, they should NOT show up.
    public function testCoreLoggingLevels()
    {
        $factory = $this->app->make(LoggerFactory::class);
        $logger = $factory->createLogger(Channels::CHANNEL_SECURITY);
        $logger->notice('User x got some new privileges.');
        $logger->info('User Y did something interesting.');
        $logger->alert('User x changed permission on page Y.');
        $logger->info('User x did something interesting.');

        $r = $this->db->GetAll('select * from Logs');
        $this->assertCount(2, $r);
        $this->assertEquals('security', $r[0]['channel']);
        $this->assertEquals('User x got some new privileges.', $r[0]['message']);
        $this->assertEquals(Logger::NOTICE, $r[0]['level']);

    }

    public function testLoggingHelper()
    {
        core_log('this is my test message');
        core_log('What is this');
        core_log('EMERGENCY!', Logger::EMERGENCY);
        core_log('This is some weird thing', Logger::INFO, Channels::CHANNEL_SECURITY);
        core_log('This is some weird thing', Logger::INFO);
        core_log('EMERGENCY 2!', Logger::EMERGENCY, Channels::CHANNEL_SECURITY);

        $r = $this->db->GetAll('select * from Logs');
        // everything makes it in except the info in the security channel.
        $this->assertCount(5, $r);
    }

    public function testMoreVerboseDatabaseLogging()
    {
        $configuration = new SimpleDatabaseConfiguration(Logger::INFO);
        $configuration->setApplication($this->getNoopProcessorApplication());

        $factory = $this->getMockBuilder(ConfigurationFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $factory->expects($this->once())
            ->method('createConfiguration')
            ->willReturn($configuration);

        $factory = $this->app->build(LoggerFactory::class, ['configurationFactory' => $factory]);
        $logger = $factory->createLogger(Channels::CHANNEL_SECURITY);

        $logger->debug('This is a debug line.');
        $logger->emergency('This is an emergency!');
        $logger->info('This is an info line.');
        $logger->notice('This is a notice line.');
        $logger->warning('This is a warning line.');

        $r = $this->db->GetAll('select * from Logs');
        $this->assertCount(4, $r);
    }

    public function testFileLogging()
    {

        $file = __DIR__ . DIRECTORY_SEPARATOR . '/testing.log';

        $configuration = new SimpleFileConfiguration($file, Logger::INFO);
        $configuration->setApplication($this->getNoopProcessorApplication());

        $factory = $this->getMockBuilder(ConfigurationFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $factory->expects($this->once())
            ->method('createConfiguration')
            ->willReturn($configuration);

        $factory = $this->app->build(LoggerFactory::class, ['configurationFactory' => $factory]);
        $logger = $factory->createLogger(Channels::CHANNEL_SECURITY);

        $logger->debug('This is a debug line.');
        $logger->emergency('This is an emergency!');
        $logger->info('This is an info line.');
        $logger->notice('This is a notice line.');
        $logger->warning('This is a warning line.', ['object' => 'foo']);

        $filesystem = new Filesystem();
        $contents = $filesystem->get($file);
        $this->assertCount(4, explode("\n", trim($contents)));
        $filesystem->delete($file);

        $this->assertCount(1, $logger->getHandlers());
        $this->assertCount(2, $logger->getProcessors()); // needs to have psr processor and the concrete5 processor.

    }

    public function testLoggingFacade()
    {
        Log::info('oh hai');
        Log::notice('testing');
        Log::warning('warning');
        Log::debug('debug');

        $r = $this->db->GetAll('select * from Logs');
        $this->assertCount(4, $r);
        $this->assertEquals('application', $r[0]['channel']);
        $this->assertEquals('oh hai', $r[0]['message']);
    }

    public function testOverridingDefaultFunctionalityWithEvents()
    {
        $factory = $this->app->make(LoggerFactory::class);
        $log = $factory->createLogger('emails');
        // should only have a database handler.
        $this->assertEquals(1, count($log->getHandlers()));

        $handler = new \Monolog\Handler\TestHandler(Logger::CRITICAL, false);
        $listener = \Events::addListener('on_logger_create', function ($event) use ($handler) {
            $logger = $event->getLogger();
            $formatter = new \Monolog\Formatter\LineFormatter();
            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);
            return $logger;
        });

        $factory = $this->app->make(LoggerFactory::class);

        $log2 = $factory->createLogger('transactions');
        $log3 = $factory->createLogger('testing');

        $this->assertEquals(count($log2->getHandlers()), 2);
        $this->assertEquals(count($log3->getHandlers()), 2);

        $log2->info('This is a test.');
        $log2->debug('This is a test.');
        $log3->debug('This is a test.');
        $log3->warning('This is a warning.');
        $log3->critical('oh boy this is big.');
        $log3->alert('Everything is broken.');
        $log3->emergency('Get out of bed.');

        $r = $this->db->GetAll('select * from Logs');
        $this->assertEquals(count($r), 4);
        $this->assertEquals(Logger::INFO, $r[0]['level']);
        $this->assertEquals('This is a test.', $r[2]['message']);
        $this->assertEquals(Logger::DEBUG, $r[2]['level']);

        $this->assertEquals(count($handler->getRecords()), 3);
        $records = $handler->getRecords();
        $this->assertEquals($records[0]['level'], Logger::CRITICAL);
        $this->assertEquals($records[1]['level'], Logger::ALERT);
        $this->assertEquals($records[2]['level'], Logger::EMERGENCY);
        $this->assertEquals($records[2]['message'], 'Get out of bed.');

        $listeners = \Events::getListeners('on_logger_create');
        \Events::removeListener('on_logger_create', $listeners[0]);
        // AND we pop the stream handler from the previous test
    }

    public function testAdvancedLoggingConfiguration()
    {
        $config = array(
            'formatters' => array(
                'spaced' => array(
                    'format' => "%datetime% %channel%.%level_name%  %message%\n",
                    'include_stacktraces' => true
                ),
                'dashed' => array(
                    'format' => "%datetime%-%channel%.%level_name% - %message%\n"
                ),
            ),
            'handlers' => array(
                'console' => array(
                    'class' => 'Monolog\Handler\StreamHandler',
                    'level' => 'DEBUG',
                    'formatter' => 'spaced',
                    'stream' => 'php://stdout'
                ),

                'info_file_handler' => array(
                    'class' => 'Monolog\Handler\StreamHandler',
                    'level' => 'INFO',
                    'formatter' => 'dashed',
                    'stream' => './demo_info.log'
                ),

                'error_file_handler' => array(
                    'class' => 'Monolog\Handler\StreamHandler',
                    'level' => 'ERROR',
                    'stream' => './demo_error.log',
                    'formatter' => 'spaced'
                )
            ),
            'processors' => array(
                'tag_processor' => array(
                    'class' => 'Monolog\Processor\TagProcessor'
                )
            ),
            'loggers' => array(
                'my_logger' => array(
                    'handlers' => array('console', 'info_file_handler')
                )
            )
        );

        $configuration = new AdvancedConfiguration($config);

        $factory = $this->getMockBuilder(ConfigurationFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $factory->expects($this->once())
            ->method('createConfiguration')
            ->willReturn($configuration);

        $factory = $this->app->build(LoggerFactory::class, ['configurationFactory' => $factory]);
        $logger = $factory->createLogger(Channels::CHANNEL_SECURITY);
        $this->assertInstanceOf(Logger::class, $logger);
        $this->assertCount(0, $logger->getHandlers());
        $this->assertCount(1, $logger->getProcessors()); // needs to have psr processor.

        $logger = $factory->createLogger('my_logger');

        $this->assertCount(2, $logger->getHandlers());
        $this->assertCount(1, $logger->getProcessors());
    }

    public function testAdvancedLoggingConfigurationAllChannels()
    {
        $config = array(
            'formatters' => array(
                'dashed' => array(
                    'format' => "%datetime%-%channel%.%level_name% - %message%\n"
                ),
            ),
            'handlers' => array(
                'info_file_handler' => array(
                    'class' => 'Monolog\Handler\StreamHandler',
                    'level' => 'INFO',
                    'formatter' => 'dashed',
                    'stream' => './demo_info.log'
                ),
            ),
            'loggers' => array(
                Channels::META_CHANNEL_ALL => array(
                    'handlers' => array('info_file_handler')
                )
            )
        );

        $configuration = new AdvancedConfiguration($config);

        $factory = $this->getMockBuilder(ConfigurationFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $factory->expects($this->once())
            ->method('createConfiguration')
            ->willReturn($configuration);

        $factory = $this->app->build(LoggerFactory::class, ['configurationFactory' => $factory]);
        $logger = $factory->createLogger(Channels::CHANNEL_SECURITY);
        $this->assertInstanceOf(Logger::class, $logger);
        $this->assertCount(1, $logger->getHandlers());
        $handlers = $logger->getHandlers();
        $formatter = $handlers[0]->getFormatter();
        $this->assertInstanceOf(LineFormatter::class, $formatter);
    }


    public function testLegacyLogSupport()
    {
        Log::addEntry('this is my log entry.');
        $le = LogEntry::getByID(1);
        $this->assertEquals($le->getLevel(), Logger::DEBUG);
        $this->assertEquals($le->getLevelName(), 'DEBUG');
        $this->assertEquals($le->getMessage(), 'this is my log entry.');

        $l = new GroupLogger(LOG_TYPE_EMAILS, Logger::NOTICE);
        $l->write('This is line one.');
        $l->write('This is line two.');

        $l2 = new GroupLogger('test', Logger::CRITICAL);
        $l2->write('OMG!');
        $l2->close();

        $l->close();

        $le2 = LogEntry::getByID(2);
        $le3 = LogEntry::getByID(3);
        $this->assertEquals($le2->getLevel(), Logger::CRITICAL);
        $this->assertEquals($le3->getLevel(), Logger::NOTICE);
        $this->assertEquals($le3->getMessage(), "This is line one.\nThis is line two.");
        $this->assertEquals($le2->getMessage(), 'OMG!');
    }

    /**
     * Build an application mock that returns noop processors
     *
     * @return M\MockInterface|Application
     */
    protected function getNoopProcessorApplication()
    {
        $noop = function($data) { return $data; };

        $app = M::mock(Application::class);
        $app->shouldReceive('make')->withArgs([Concrete5UserProcessor::class])->andReturn($noop);
        $app->shouldReceive('make')->withArgs([PsrLogMessageProcessor::class])->andReturn($noop);

        return $app;
    }


}
