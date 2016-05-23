<?php
namespace Concrete\Core\Database\Connection;

use Doctrine\ORM\EntityManager;
use ORM;

class Connection extends \Doctrine\DBAL\Connection
{
    /** @var EntityManager */
    protected $entityManager;

    /**
     * @deprecated Please use the ORM facade instead of this method:
     * - ORM::entityManager() in the application/site code and core
     * - $pkg->getEntityManager() in packages
     * @return EntityManager
     */
    public function getEntityManager()
    {
        if (!$this->entityManager) {
            $this->entityManager = $this->createEntityManager();
        }

        return $this->entityManager;
    }

    /**
     * @return EntityManager
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function createEntityManager()
    {
        return ORM::entityManager();
    }

    /**
     * Returns true if a table exists – is NOT case sensitive.
     *
     * @return bool
     */
    public function tableExists($tableName)
    {
        $sm = $this->getSchemaManager();
        $schemaTables = $sm->listTableNames();

        return in_array(strtolower($tableName), array_map('strtolower', $schemaTables));
    }

    /**
     * @deprecated
     */
    public function Execute($q, $arguments = array())
    {
        if ($q instanceof \Doctrine\DBAL\Statement) {
            return $q->execute($arguments);
        } else {
            if (!is_array($arguments)) {
                $arguments = array($arguments); // adodb backward compatibility
            }

            return $this->executeQuery($q, $arguments);
        }
    }

    public function query()
    {
        $args = func_get_args();
        if (isset($args) && isset($args[1]) && (is_string($args[1]) || is_array($args[1]))) {
            return $this->executeQuery($args[0], $args[1]);
        } else {
            return call_user_func_array('parent::query', $args);
        }
    }

    /**
     * @deprecated
     * alias to old ADODB method
     */
    public function GetRow($q, $arguments = array())
    {
        if (!is_array($arguments)) {
            $arguments = array($arguments); // adodb backward compatibility
        }
        $r = $this->fetchAssoc($q, $arguments);
        if (!is_array($r)) {
            $r = array();
        }

        return $r;
    }

    /**
     * @deprecated
     * alias to old ADODB method
     */
    public function qstr($string)
    {
        return $this->quote($string);
    }

    /**
     * @deprecated
     * alias to old ADODB method
     */
    public function GetOne($q, $arguments = array())
    {
        if (!is_array($arguments)) {
            $arguments = array($arguments); // adodb backward compatibility
        }

        return $this->fetchColumn($q, $arguments, 0);
    }

    /**
     * @deprecated
     * alias to old ADODB method
     */
    public function ErrorMsg()
    {
        if ($this->errorCode() > 0) {
            return $this->errorCode();
        }

        return false;
    }

    /**
     * @deprecated
     * alias to old ADODB method
     */
    public function GetAll($q, $arguments = array())
    {
        if (!is_array($arguments)) {
            $arguments = array($arguments); // adodb backward compatibility
        }

        return $this->fetchAll($q, $arguments);
    }

    /**
     * @deprecated
     * alias to old ADODB method
     */
    public function GetArray($q, $arguments = array())
    {
        return $this->GetAll($q, $arguments);
    }

    /**
     * @deprecated
     * alias to old ADODB method
     */
    public function GetAssoc($q, $arguments = array())
    {
        $query = $this->query($q, $arguments);

        return $query->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    /**
     * @deprecated
     * Returns an associative array of all columns in a table
     */
    public function MetaColumnNames($table)
    {
        $sm = $this->getSchemaManager();
        $columnNames = array();
        $columns = $sm->listTableColumns($table);
        foreach ($columns as $column) {
            $columnNames[] = $column->getName();
        }

        return $columnNames;
    }

    /**
     * @deprecated
     * Alias to old ADODB Replace() method.
     */
    public function Replace($table, $fieldArray, $keyCol, $autoQuote = true)
    {
        $qb = $this->createQueryBuilder();
        $qb->select('count(*)')->from($table, 't');
        $where = $qb->expr()->andX();
        $updateKeys = array();
        if (!is_array($keyCol)) {
            $keyCol = array($keyCol);
        }
        foreach ($keyCol as $key) {
            if (isset($fieldArray[$key])) {
                $field = $fieldArray[$key];
            } else {
                $field = null;
            }
            $updateKeys[$key] = $field;
            if ($autoQuote) {
                $field = $qb->expr()->literal($field);
            }
            $where->add($qb->expr()->eq($key, $field));
        }
        $qb->where($where);
        $num = $this->query($qb->getSql())->fetchColumn();
        if ($num < 1) {
            $this->insert($table, $fieldArray);
        } else {
            $this->update($table, $fieldArray, $updateKeys);
        }
    }

    /**
     * @deprecated -
     * alias to old ADODB method
     */
    public function GetCol($q, $arguments = array())
    {
        $r = $this->fetchAll($q, $arguments);
        $return = array();

        foreach ($r as $value) {
            $return[] = $value[key($value)];
        }

        return $return;
    }

    /**
     * @deprecated
     * alias to old ADODB method
     */
    public function Insert_ID()
    {
        return $this->lastInsertId();
    }

    /**
     * @deprecated
     */
    public function MetaTables()
    {
        $sm = $this->getSchemaManager();
        $schemaTables = $sm->listTables();
        $tables = array();
        foreach ($schemaTables as $table) {
            $tables[] = $table->getName();
        }

        return $tables;
    }

    /**
     * @deprecated
     */
    public function MetaColumns($table)
    {
        $sm = $this->getSchemaManager();
        $schemaColumns = $sm->listTableColumns($table);

        return $schemaColumns;
    }

    /**
     * @deprecated Alias to old ADODB method
     */
    public function BeginTrans()
    {
        $this->beginTransaction();

        return true;
    }

    /**
     * @deprecated Alias to old ADODB method
     */
    public function StartTrans()
    {
        $this->beginTransaction();

        return true;
    }

    /**
     * @deprecated Alias to old ADODB method
     */
    public function CommitTrans()
    {
        $this->commit();

        return true;
    }

    /**
     * @deprecated Alias to old ADODB method
     */
    public function CompleteTrans()
    {
        $this->commit();

        return true;
    }

    /**
     * @deprecated Alias to old ADODB method
     */
    public function RollbackTrans()
    {
        $this->rollBack();

        return true;
    }

    /**
     * @deprecated Alias to old ADODB method
     */
    public function FailTrans()
    {
        $this->rollBack();

        return true;
    }
}
