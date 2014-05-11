<?
namespace Concrete\Core\Logging;

use Concrete\Core\Logging\Handler\DatabaseHandler;
use \Monolog\Logger as MonologLogger;

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
    }
}