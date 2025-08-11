<?php
namespace Concrete\Core\Logging\Handler;

use Concrete\Core\Database\Connection\Connection;
use Monolog\Handler\AbstractProcessingHandler;

class DatabaseHandler extends AbstractProcessingHandler
{
    use HandlerTrait;

    /**
     * @var bool
     */
    protected $initialized = false;

    /**
     * @var \Doctrine\DBAL\Driver\Statement
     */
    private $statement;

    /**
     * Clears all log entries. Requires the database handler.
     */
    public static function clearAll()
    {
        $db = app(Connection::class);
        $db->executeStatement('delete from Logs');
    }

    /**
     * Clears log entries by channel. Requires the database handler.
     *
     * @param $channel string
     */
    public static function clearByChannel($channel)
    {
        $db = app(Connection::class);
        $db->delete('Logs', ['channel' => $channel]);
    }

    protected function write(array $record)
    {
        if (!$this->shouldWrite($record)) {
            return;
        }
        if (!$this->initialized) {
            $this->initialize();
        }
        $params = [
            'channel' => $record['channel'],
            'level' => $record['level'],
            'message' => $record['formatted'],
            'time' => $record['datetime']->format('U'),
            'uID' => $record['extra']['user'][0] ?? 0,
            'cID' => $record['extra']['page'][0] ?? 0,
        ];
        try {
            $this->statement->execute($params);
        } catch (\Doctrine\DBAL\Exception\InvalidFieldNameException $x) {
            \Concrete\Core\Database\Schema\Schema::refreshCoreXMLSchema(['Logs']);
            $this->statement->execute($params);
        }
    }

    private function initialize()
    {
        $db = app(Connection::class);

        $this->statement = $db->prepare(
            'INSERT INTO Logs (channel, level, message, time, uID, cID) VALUES (:channel, :level, :message, :time, :uID, :cID)'
        );

        $this->initialized = true;
    }
}
