<?php
namespace Concrete\Core\Database\Driver;

/**
 * The PDO implementation of the Statement interface.
 * Used by all PDO-based drivers.
 *
 * @since 2.0
 */
class PDOStatement extends \Doctrine\DBAL\Driver\PDOStatement
{

    /**
     * @deprecated
     * alias to old ADODB result method
     */
    public function fetchRow()
    {
        return $this->fetch();
    }

    /**
     * @deprecated
     * alias to old ADODB method
     */
    public function Close()
    {
        return $this->closeCursor();
    }

    /**
     * @deprecated
     * alias to old ADODB method
     */
    public function free()
    {
        return $this->closeCursor();
    }

    /**
     * @deprecated
     * alias to old ADODB result method
     */
    public function numRows()
    {
        return $this->rowCount();
    }

}
