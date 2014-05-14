<?
namespace Concrete\Core\Logging;

use Concrete\Core\Logging\Handler\DatabaseHandler;
use \Monolog\Logger as MonologLogger;
use \Monolog\Formatter\LineFormatter;
use Database;
use Events;

class Logger extends MonologLogger
{

    const CHANNEL_APPLICATION = 'application';

    public function __construct($channel = self::CHANNEL_APPLICATION) {
        parent::__construct($channel);
        $this->addDatabaseHandler();

        $le = new Event($this);
        Events::dispatch('on_logger_create', $le);
    }

    /**
     * Initially called - this sets up the log writer to use the concrete5
     * Logs database table (this is the default setting.)
     */
    public function addDatabaseHandler()
    {
        $handler = new DatabaseHandler();
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
        $channels = (array) $db->FetchAssoc('select distinct channel from Logs order by channel asc');
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

}