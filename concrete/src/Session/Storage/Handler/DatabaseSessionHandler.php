<?php

namespace Concrete\Core\Session\Storage\Handler;

use Concrete\Core\Support\Facade\Database;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

class DatabaseSessionHandler extends PdoSessionHandler
{

    /**
     * @param int $maxlifetime
     * @return bool
     */
    public function gc($maxlifetime = 7200)
    {

        $db = Database::connection();
        $sql = "DELETE FROM Sessions WHERE sessionLifeTime + sessionTime < :time";

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':time', time(), \PDO::PARAM_INT);
        $stmt->execute();

        return true;

    }

}
