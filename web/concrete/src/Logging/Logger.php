<?php
namespace Concrete\Core\Logging;

use Concrete\Core\Logging\Handler\DatabaseHandler;
use \Monolog\Logger as MonologLogger;
use \Monolog\Formatter\LineFormatter;
use Database;
use Events;
use Core;
use Monolog\Processor\PsrLogMessageProcessor;

class Logger extends MonologLogger
{

    const CHANNEL_APPLICATION = 'application';

    public function __construct($channel = self::CHANNEL_APPLICATION, $logLevel = MonologLogger::DEBUG)
    {
        parent::__construct($channel);
        $this->addDatabaseHandler($logLevel);
        $this->pushProcessor(new PsrLogMessageProcessor());

        $le = new Event($this);
        Events::dispatch('on_logger_create', $le);
    }

    /**
     * Initially called - this sets up the log writer to use the concrete5
     * Logs database table (this is the default setting.)
     */
    public function addDatabaseHandler($logLevel = MonologLogger::DEBUG)
    {
        $handler = new DatabaseHandler($logLevel);
        // set a more basic formatter.
        $output = "%message%";
        $formatter = new LineFormatter($output, null, true);
        $handler->setFormatter($formatter);
        $this->pushHandler($handler);
    }


    /**
     * When given a PSR-3 standard log level, returns the
     * internal code for that level.
     */
    public static function getLevelCode($level)
    {
        $levels = static::getLevels();
        $level = strtoupper($level);
        if (isset($levels[$level])) {
            return $levels[$level];
        }
    }

    /**
     * Returns an array of handlers. Mostly for testing.
     */
    public function getHandlers()
    {
        return $this->handlers;
    }

    /**
     * Returns a list of channels that have been used. Requires the database
     *  handler
     */
    public function getChannels()
    {
        $db = Database::get();
        $channels = (array) $db->GetCol('select distinct channel from Logs order by channel asc');
        return $channels;
    }


    /**
     * Clears all log entries. Requires the database handler.
     */
    public static function clearAll()
    {
        $db = Database::get();
        $db->Execute('delete from Logs');
    }

    /**
     * Clears log entries by channel. Requires the database handler.
     */
    public static function clearByChannel($channel)
    {
        $db = Database::get();
        $db->delete('Logs', array('channel' => $channel));
    }

    /**
     * Gets the name of the logging level.
     *
     * @param  integer $level
     * @return string
     */
    public static function getLevelDisplayName($level)
    {
        if (array_key_exists($level, static::$levels)) {
            switch (static::$levels[$level]) {
                case 'DEBUG':
                    return tc(/*i18n: Detailed debug information */ 'Log level', 'Debug');
                case 'INFO':
                    return tc(/*i18n: Interesting events */ 'Log level', 'Info');
                case 'NOTICE':
                    return tc(/*i18n: Uncommon events */ 'Log level', 'Notice');
                case 'WARNING':
                    return tc(/*i18n: Exceptional occurrences that are not errors */ 'Log level', 'Warning');
                case 'ERROR':
                    return tc(/*i18n: Runtime errors */ 'Log level', 'Error');
                case 'CRITICAL':
                    return tc(/*i18n: Critical conditions */ 'Log level', 'Critical');
                case 'ALERT':
                    return tc(/*i18n: Action must be taken immediately */ 'Log level', 'Alert');
                case 'EMERGENCY':
                    return tc(/*i18n: Urgent alert */ 'Log level', 'Emergency');
            }
        }
        return tc(/*i18n: Urgent alert */ 'Log level', ucfirst(strtolower($level)));
    }

    public static function getChannelDisplayName($channel)
    {
        switch ($channel) {
            case 'application':
                return tc('Log channel', 'Application');
            case LOG_TYPE_EMAILS:
                return tc('Log channel', 'Sent Emails');
            case LOG_TYPE_EXCEPTIONS:
                return tc('Log channel', 'Exceptions');
            default:
                return tc('Log channel', Core::make('helper/text')->unhandle($channel));
        }
    }


}
