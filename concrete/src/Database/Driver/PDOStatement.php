<?php
namespace Concrete\Core\Database\Driver;

/**
 * The PDO implementation of the Statement interface.
 * Used by all PDO-based drivers.
 *
 * @method @deprecated void free()
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
     * alias to old ADODB result method
     */
    public function numRows()
    {
        return $this->rowCount();
    }

    public function __call($name, $arguments)
    {
        switch (strtolower($name)) {
            case 'free':
                $this->closeCursor();
                return;
            default:
                $msg = sprintf('Call to undefined method %s::%s()', get_class(), $name);
                if (class_exists('Error')) {
                    throw new \Error($msg);
                }
                trigger_error($msg, E_USER_ERROR);
        }
    }
}
