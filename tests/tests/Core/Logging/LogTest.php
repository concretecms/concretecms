<?php

use \Concrete\Core\Logging\Logger;
use \Concrete\Core\Logging\Event;
use \Concrete\Core\Logging\LogEntry;
use \Concrete\Core\Logging\GroupLogger;

class LogTest extends ConcreteDatabaseTestCase
{

    protected $fixtures = array();
    protected $tables = array('Logs');

    public function testBasicWrite()
    {
        $l = new Logger('sample-channel');
        $l->debug('This is a debug line.');
        $l->emergency('This is an emergency');

        Log::critical('Critical error found.');

        // now we determine if writing occurred successfully.
        $db = Database::get();
        $r = $db->GetAll('select * from Logs');
        $this->assertEquals(1, $r[0]['logID']);
        $this->assertEquals('sample-channel', $r[0]['channel']);
        $this->assertEquals('This is a debug line.', $r[0]['message']);
        $this->assertEquals(Log::getLevelCode('debug'), $r[0]['level']);

        $this->assertEquals('This is an emergency', $r[1]['message']);
        $this->assertEquals(Log::getLevelCode('emergency'), $r[1]['level']);

        $this->assertEquals(3, $r[2]['logID']);
        $this->assertEquals('Critical error found.', $r[2]['message']);
        $this->assertEquals(Log::getLevelCode('critical'), $r[2]['level']);
    }


    /**
     * Attempts to change the default database logging functionality
     * into a file stream.
     */
    public function testOverridingDefaultLogFunctionalityWithFileHandler()
    {
        if (file_exists(dirname(__FILE__) . '/test.log')) {
            unlink(dirname(__FILE__) . '/test.log');
        }

        Log::info('This should be in the database.');

        // now we will add a stream handler that can handle all the different
        // types of debug messages, but it should keep things OUT of the database
        $r = new stdClass;
        $r->test = 'test';

        $sh = new \Monolog\Handler\StreamHandler(dirname(__FILE__) . '/test.log', Logger::DEBUG, false);
        Log::pushHandler($sh);
        Log::warning('This is a warning!');
        Log::info('This is an interesting object', array($r));

        $db = Database::get();
        $r = $db->GetAll('select * from Logs');
        // there should only be one item in the logs table because the first info
        // should be in there but the rest should not be.
        $this->assertTrue($r[0]['logID'] == 1);
        $this->assertTrue($r[0]['channel'] == Logger::CHANNEL_APPLICATION);
        $this->assertTrue($r[0]['message'] == 'This should be in the database.');
        $this->assertTrue($r[0]['level'] == Log::getLevelCode('info'));

        $this->assertEquals(count($r), 1);

        $sh->close();
        $contents = trim(file_get_contents(dirname(__FILE__) . '/test.log'));
        $entries = explode("\n", $contents);

        $this->assertEquals(count($entries), 2);

        if (file_exists(dirname(__FILE__) . '/test.log')) {
            unlink(dirname(__FILE__) . '/test.log');
        }


    }

    public function testOverringDefaultFunctionalityWithEvents()
    {
        $log = new Logger('emails');
        // should only have a database handler.
        $this->assertEquals(count($log->getHandlers()), 1);
        $this->assertEquals(count(Log::getHandlers()),
            2);// this should still have the same stream handler from last test.

        $handler = new \Monolog\Handler\TestHandler(Logger::CRITICAL, false);
        $listener = Events::addListener('on_logger_create', function ($event) use ($handler) {
            $logger = $event->getLogger();
            $formatter = new \Monolog\Formatter\LineFormatter();
            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);
            return $logger;
        });

        $log2 = new Logger('transactions');
        $log3 = new Logger('testing');
        $this->assertEquals(count($log2->getHandlers()), 2);
        $this->assertEquals(count($log3->getHandlers()), 2);

        $log2->info('This is a test.');
        $log2->debug('This is a test.');
        $log3->debug('This is a test.');
        $log3->critical("oh boy this is big.");
        $log3->alert("Everything is broken.");
        $log3->emergency("Get out of bed.");

        $db = Database::get();
        $r = $db->GetAll('select * from Logs');
        $this->assertEquals(count($r), 3); // only the non-critical, non-alert, non-emergency items.


        $this->assertEquals(count($handler->getRecords()), 3);
        $records = $handler->getRecords();
        $this->assertEquals($records[0]['level'], Logger::CRITICAL);
        $this->assertEquals($records[1]['level'], Logger::ALERT);
        $this->assertEquals($records[2]['level'], Logger::EMERGENCY);
        $this->assertEquals($records[2]['message'], 'Get out of bed.');

        $listeners = Events::getListeners('on_logger_create');
        Events::removeListener('on_logger_create', $listeners[0]);
        // AND we pop the stream handler from the previous test
        Log::popHandler();

    }

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
        Log::addEntry("this is my log entry.");
        $le = LogEntry::getByID(1);
        $this->assertEquals($le->getLevel(), Logger::DEBUG);
        $this->assertEquals($le->getLevelName(), 'DEBUG');
        $this->assertEquals($le->getMessage(), 'this is my log entry.');

        /*
         * old format here:
        $l = new Log(LOG_TYPE_EMAILS, true, true);
        $l->write('This is line one.');
        $l->write('This is line two');
        $l->close();
        */

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
        $this->assertEquals($le2->getMessage(), "OMG!");
    }

}