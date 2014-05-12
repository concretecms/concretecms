<?
namespace Concrete\Core\Logging;

use Concrete\Core\Logging\Handler\DatabaseHandler;
use \Monolog\Logger as MonologLogger;
use \Monolog\Formatter\LineFormatter;

class Logger
{

    public function __construct($channel = false)
    {
        $this->library = new MonologLogger($channel);
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
        $formatter = new LineFormatter($output);
        $handler->setFormatter($formatter);
        $this->library->pushHandler($handler);
    }

    protected $levels = array(
        MonologLogger::EMERGENCY => 'emergency',
        MonologLogger::ALERT => 'alert',
        MonologLogger::CRITICAL => 'critical',
        MonologLogger::ERROR => 'error',
        MonologLogger::WARNING => 'warning',
        MonologLogger::NOTICE => 'notice',
        MonologLogger::INFO => 'info',
        MonologLogger::DEBUG => 'debug'
    );

    public function __call($nm, $args)
    {
        if (in_array($nm, $this->levels)) {
            $writeMethod = 'add' . ucfirst($nm);
            return call_user_func_array(array($this->library, $writeMethod), $args);
        }
    }

    /**
     * When given a PSR-3 standard log level, returns the
     * internal code for that level.
     */
    public function getLevelCode($code)
    {
        if (in_array($code, $this->levels)) {
            return array_search($code, $this->levels);
        }
    }
}