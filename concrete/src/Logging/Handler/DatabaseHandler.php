<?php

namespace Concrete\Core\Logging\Handler;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Database;
use User;

class DatabaseHandler extends AbstractProcessingHandler
{
    protected $initialized;
    private $statement;

    protected function write(array $record)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        $u = new User();
        $uID = ($u->isRegistered()) ? $u->getUserID() : 0;

        $this->statement->execute(
            array(
                'channel' => $record['channel'],
                'level' => $record['level'],
                'message' => $record['formatted'],
                'time' => $record['datetime']->format('U'),
                'uID' => $uID
            )
        );
    }

    private function initialize()
    {
        $db = Database::get();

        $this->statement = $db->prepare(
            'INSERT INTO Logs (channel, level, message, time, uID) VALUES (:channel, :level, :message, :time, :uID)'
        );

        $this->initialized = true;
    }
}
