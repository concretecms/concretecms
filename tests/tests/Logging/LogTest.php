<?php

namespace Concrete\Tests\Logging;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Support\Facade\Log;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

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

    public function testStandardDatabaseLogging()
    {
        $factory = $this->app->make(LoggerFactory::class);
        $logger = $factory->createLogger('sample-channel');
        $this->assertInstanceOf(Logger::class, $logger);

        $logger->debug('This is a debug line.');
        $logger->emergency('This is an emergency!');
        $logger->info('This is an info line.');
        $logger->notice('This is a notice line.');
        $logger->warning('This is a warning line.');

        // now we determine if writing occurred successfully.
        // Note, by default we now no longer log anything below warning, so our tests should show this.
        $r = $this->db->GetAll('select * from Logs');
        $this->assertEquals(1, $r[0]['logID']);
        $this->assertEquals('sample-channel', $r[0]['channel']);
        $this->assertEquals('This is an emergency!', $r[0]['message']);
        $this->assertEquals('sample-channel', $r[1]['channel']);
        $this->assertEquals('This is a warning line.', $r[1]['message']);
        $this->assertCount(2, $r);
    }

    public function testMoreVerboseDatabaseLogging()
    {
        $repository = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $return = [
            'simple' => [
                'enabled' => true,
                'level' => 'INFO',
            ]
        ];

        $repository->expects($this->once())
            ->method('get')
            ->will(
                $this->returnValueMap(
                    array(
                        array('concrete.log.configurations', null, $return),
                    )
                )
            );

        $factory = new LoggerFactory($repository, $this->app->make('director'));
        $logger = $factory->createLogger('verbose-channel');

        $logger->debug('This is a debug line.');
        $logger->emergency('This is an emergency!');
        $logger->info('This is an info line.');
        $logger->notice('This is a notice line.');
        $logger->warning('This is a warning line.');

        $r = $this->db->GetAll('select * from Logs');
        $this->assertCount(4, $r);
    }

    public function testLoggingFacade()
    {
        Log::info('oh hai');
        Log::notice('testing');
        Log::warning('warning');
        Log::debug('debug');

        $r = $this->db->GetAll('select * from Logs');
        $this->assertCount(1, $r);
        $this->assertEquals('application', $r[0]['channel']);
        $this->assertEquals('warning', $r[0]['message']);
    }

    public function testOverridingDefaultLogFunctionalityWithFileHandler()
    {
        if (file_exists(__DIR__ . '/test.log')) {
            unlink(__DIR__ . '/test.log');
        }

        Log::error('This should be in the database.');

        // now we will add a stream handler that can handle all the different
        // types of debug messages, but it should keep things OUT of the database
        $r = new \stdClass();
        $r->test = 'test';

        $sh = new StreamHandler(__DIR__ . '/test.log', Logger::DEBUG, false);
        $logger = $this->app->make(LoggerFactory::class)->getApplicationLogger();
        $logger->pushHandler($sh);
        Log::warning('This is a warning!');
        Log::info('This is an interesting object', [$r]);

        $r = $this->db->GetAll('select * from Logs');
        // there should only be one item in the logs table because the first info
        // should be in there but the rest should not be.
        $this->assertEquals(1, $r[0]['logID']);
        $this->assertEquals(LoggerFactory::CHANNEL_APPLICATION, $r[0]['channel'] );
        $this->assertEquals('This should be in the database.', $r[0]['message']);

        $this->assertEquals(count($r), 1);

        $sh->close();
        $contents = trim(file_get_contents(__DIR__ . '/test.log'));
        $entries = explode("\n", $contents);

        $this->assertEquals(count($entries), 2);

        if (file_exists(__DIR__ . '/test.log')) {
            unlink(__DIR__ . '/test.log');
        }
    }

    public function testMoreElegantCustomHandlerSetup()
    {
        $factory = $this->app->make(LoggerFactory::class);
        $factory->setLoggerHandlers([new NullHandler()]);
        $logger = $factory->createLogger('testing');
        $logger->error('This should not be written anywhere.');

        $r = $this->db->GetAll('select * from Logs');
        $this->assertEquals(count($r), 0);

        $factory->setLoggerHandlers([]);
    }



    public function testOverringDefaultFunctionalityWithEvents()
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
        $this->assertEquals(count($r), 1);

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



    /*
    public function testLogEntryObject()
    {
        Log::info('This is an info');
        $db = Database::get();
        $le = LogEntry::getByID(1);
        $this->assertEquals($le->getID(), 1);
        $this->assertEquals($le->getLevel(), Logger::INFO);
        $this->assertEquals($le->getLevelName(), 'INFO');
        $this->assertEquals($le->getMessage(), 'This is an info');
    }

    public function testLegacyLogSupport()
    {
        Log::addEntry('this is my log entry.');
        $le = LogEntry::getByID(1);
        $this->assertEquals($le->getLevel(), Logger::DEBUG);
        $this->assertEquals($le->getLevelName(), 'DEBUG');
        $this->assertEquals($le->getMessage(), 'this is my log entry.');

        $l = new GroupLogger(LOG_TYPE_EMAILS, Logger::DEBUG);
        $l->write('This is line one.');
        $l->write('This is line two.');

        $l2 = new GroupLogger('test', Logger::CRITICAL);
        $l2->write('OMG!');
        $l2->close();

        $l->close();

        $le2 = LogEntry::getByID(2);
        $le3 = LogEntry::getByID(3);
        $this->assertEquals($le2->getLevel(), Logger::CRITICAL);
        $this->assertEquals($le3->getLevel(), Logger::DEBUG);
        $this->assertEquals($le3->getMessage(), "This is line one.\nThis is line two.");
        $this->assertEquals($le2->getMessage(), 'OMG!');
    }
    */
}
