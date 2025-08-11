<?php /** @noinspection PhpUnused */
/** @noinspection SqlDialectInspection */
/** @noinspection SqlNoDataSourceInspection */

namespace Concrete\Core\User\Group;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Support\Facade\Application;
use Doctrine\DBAL\Exception;
use JsonSerializable;

class GroupRole extends ConcreteObject implements JsonSerializable
{
    /** @var int */
    protected $grID;
    /** @var string */
    protected $grName;
    /** @var bool */
    protected $grIsManager;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->grID;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->grName;
    }

    /**
     * @param string $grName
     * @return bool
     */
    public function setName($grName)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $this->grName = $grName;

        try {
            $db->executeQuery("update GroupRoles set grName = ? where grID = ?", [$grName, $this->grID]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isManager()
    {
        return (bool)$this->grIsManager;
    }

    /**
     * @param bool $grIsManager
     * @return bool
     */
    public function setIsManager($grIsManager)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $this->grIsManager = $grIsManager;

        try {
            $db->executeQuery("update GroupRoles set grIsManager = ? where grID = ?", [(int)$grIsManager, $this->grID]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $grID
     * @return static|bool
     */
    public static function getByID($grID)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        try {
            $row = $db->fetchAssoc('select grID, grName, grIsManager from GroupRoles where grID = ?', [$grID]);
        } catch (Exception $e) {
            return false;
        }

        if (isset($row['grID'])) {
            $gs = new static();
            $gs->setPropertiesFromArray($row);

            return $gs;
        } else {
            return false;
        }
    }

    /**
     * @param GroupType $groupType
     * @return static[]
     */
    public static function getListByGroupType(GroupType $groupType)
    {
        $list = [];

        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        try {
            $rows = $db->fetchAll('select grID from GroupTypeSelectedRoles where gtID = ?', [$groupType->getId()]);

            foreach ($rows as $row) {
                $list[] = static::getByID($row['grID']);
            }
        } catch (Exception $e) {
        }

        return $list;
    }

    /**
     * @param Group $group
     * @return array
     */
    public static function getListByGroup(Group $group)
    {
        $list = [];

        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        try {
            $rows = $db->fetchAll('select grID from GroupSelectedRoles where gID = ?', [$group->getGroupID()]);

            foreach ($rows as $row) {
                $list[] = static::getByID($row['grID']);
            }
        } catch (Exception $e) {
        }

        return $list;
    }

    /**
     * @return array
     */
    public static function getList()
    {
        $list = [];

        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        try {
            $rows = $db->fetchAll('select grID from GroupRoles order by grName asc');

            foreach ($rows as $row) {
                $list[] = static::getByID($row['grID']);
            }
        } catch (Exception $e) {
        }

        return $list;
    }

    /**
     * @param string $grName
     * @param bool $grIsManager
     * @return bool|static
     */
    public static function add($grName, $grIsManager)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        try {
            $db->executeQuery('insert into GroupRoles (grName, grIsManager) values (?,?)', [$grName, (int)$grIsManager]);
        } catch (Exception $e) {
            return false;
        }

        $id = $db->lastInsertId();

        return self::getByID($id);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function delete()
    {
        if ($this->getId() == DEFAULT_GROUP_ROLE_ID) {
            throw new \Exception(t("You can't delete the default role."));
        }

        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        try {
            $db->executeQuery('delete from GroupRoles where grID = ?', [$this->getId()]);
            $db->executeQuery('delete from GroupSelectedRoles where grID = ?', [$this->getId()]);
            $db->executeQuery('delete from GroupTypeSelectedRoles where grID = ?', [$this->getId()]);

            // update Group Type or Group => set new default role
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            "id" => $this->getId(),
            "name" => $this->getName(),
            "manager" => $this->isManager(),
        ];
    }
}