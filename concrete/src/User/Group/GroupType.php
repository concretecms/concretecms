<?php /** @noinspection PhpUnused */
/** @noinspection SqlDialectInspection */
/** @noinspection SqlNoDataSourceInspection */

namespace Concrete\Core\User\Group;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Support\Facade\Application;
use Doctrine\DBAL\Exception;
use JsonSerializable;

class GroupType extends ConcreteObject implements JsonSerializable
{
    /** @var int */
    protected $gtID;
    /** @var string */
    protected $gtName;
    /** @var bool */
    protected $gtPetitionForPublicEntry;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->gtID;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->gtName;
    }

    /**
     * @param string $gtName
     * @return bool
     */
    public function setName($gtName)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $this->gtName = $gtName;

        try {
            $db->executeQuery("update GroupTypes set gtName = ? where gtID = ?", [$gtName, $this->gtID]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isPetitionForPublicEntry()
    {
        return (bool)$this->gtPetitionForPublicEntry;
    }

    /**
     * @param bool $gtPetitionForPublicEntry
     * @return bool
     */
    public function setPetitionForPublicEntry($gtPetitionForPublicEntry)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $this->gtPetitionForPublicEntry = $gtPetitionForPublicEntry;

        try {
            $db->executeQuery("update GroupTypes set gtPetitionForPublicEntry = ? where gtID = ?", [(int)$gtPetitionForPublicEntry, $this->gtID]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $gtID
     * @return static|bool
     */
    public static function getByID($gtID)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        try {
            $row = $db->fetchAssoc('select gtID, gtName, gtPetitionForPublicEntry, gtDefaultRoleID from GroupTypes where gtID = ?', [$gtID]);
        } catch (Exception $e) {
            return false;
        }

        if (isset($row['gtID'])) {
            $gs = new static();
            $gs->setPropertiesFromArray($row);

            return $gs;
        } else {
            return false;
        }
    }

    /**
     * @return bool|GroupRole
     */
    public function getDefaultRole()
    {
        Return GroupRole::getByID($this->gtDefaultRoleID);
    }

    /**
     * @return array
     */
    public static function getSelectList()
    {
        $list = [];

        foreach (self::getList() as $groupType) {
            $list[$groupType->getId()] = $groupType->getName();
        }

        return $list;
    }

    /**
     * @return GroupType[]
     */
    public static function getList()
    {
        $list = [];

        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        try {
            $rows = $db->fetchAll('select gtID from GroupTypes order by gtName asc');

            foreach ($rows as $row) {
                $list[] = static::getByID($row['gtID']);
            }
        } catch (Exception $e) {
        }

        return $list;
    }

    /**
     * @param string $gtName
     * @param bool $gtPetitionForPublicEntry
     * @return bool|static
     */
    public static function add($gtName, $gtPetitionForPublicEntry)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        try {
            $db->executeQuery('insert into GroupTypes (gtName, gtPetitionForPublicEntry) values (?,?)', [$gtName, (int)$gtPetitionForPublicEntry]);
        } catch (Exception $e) {
            return false;
        }

        $id = $db->lastInsertId();

        return self::getByID($id);
    }

    /**
     * @param GroupRole $role
     * @return bool
     */
    public function setDefaultRole($role)
    {
        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        $this->gtDefaultRoleID = $role->getId();

        try {
            $db->executeQuery("update GroupTypes set gtDefaultRoleID = ? where gtID = ?", [(int)$this->gtDefaultRoleID, $this->gtID]);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function delete()
    {
        if ($this->getId() == DEFAULT_GROUP_TYPE_ID) {
            throw new \Exception(t("You can't delete the default group type."));
        }

        foreach ($this->getRoles() as $role) {
            $role->delete();
        }

        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        try {
            $db->executeQuery('delete from GroupTypes where gtID = ?', [$this->getId()]);
            $db->executeQuery("update `Groups` set gtID = ? where gtID = ?", [DEFAULT_GROUP_TYPE_ID, $this->getId()]);
            $db->executeQuery("update UserGroups set gtID = ? where gtID = ?", [DEFAULT_GROUP_TYPE_ID, $this->getId()]);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @return GroupRole[]
     */
    public function getRoles()
    {
        return GroupRole::getListByGroupType($this);
    }

    /**
     * @param GroupRole $role
     * @return bool
     */
    public function addRole($role)
    {

        $app = Application::getFacadeApplication();
        /** @var Connection $db */
        $db = $app->make(Connection::class);

        try {
            $db->executeQuery('insert into GroupTypeSelectedRoles (grID, gtID) values (?,?)', [(int)$role->getId(), (int)$this->getId()]);
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
            "isPetitionForPublicEntry" => $this->isPetitionForPublicEntry(),
            "defaultRole" => $this->getDefaultRole(),
            "roles" => $this->getRoles()
        ];
    }
}