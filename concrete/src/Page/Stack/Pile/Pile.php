<?php
namespace Concrete\Core\Page\Stack\Pile;

use Concrete\Core\Block\Block;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Page;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Application;
use Loader;

/**
 * Class Pile.
 *
 * Essentially a user's scrapbook, a pile is an object used for clumping bits of content together around a user account.
 * Piles currently only contain blocks but they could also contain collections. Any bit of content inside a user's pile
 * can be reordered, etc... although no public interface makes use of much of this functionality.
 *
 * \@package Concrete\Core\Page\Stack\Pile
 */
class Pile extends ConcreteObject
{
    /**
     * @var int
     */
    public $pID;

    /**
     * @var int
     */
    public $uID;

    /**
     * @var bool
     */
    public $isDefault;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $state;

    /**
     * @var string
     */
    public $timestamp;

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->isDefault;
    }

    /**
     * @return string
     */
    public function getPileName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPileState()
    {
        return $this->state;
    }

    /**
     * @param $name
     *
     * @return Pile
     */
    public static function create($name)
    {
        $app = Application::getFacadeApplication();
        $db = Loader::db();
        $u = $app->make(User::class);
        $v = array($u->getUserID(), 0, $name, 'READY');
        $q = "insert into Piles (uID, isDefault, name, state) values (?, ?, ?, ?)";
        $r = $db->query($q, $v);
        if ($r) {
            $pID = $db->Insert_ID();

            return self::get($pID);
        }
    }

    /**
     * @param int $pID
     *
     * @return Pile
     */
    public static function get($pID)
    {
        $db = Loader::db();
        $v = array($pID);
        $q = "select pID, uID, isDefault, name, state from Piles where pID = ?";
        $r = $db->query($q, $v);
        $row = $r->fetch();

        $p = new self();
        if (is_array($row)) {
            foreach ($row as $k => $v) {
                $p->{$k} = $v;
            }
        }

        return $p;
    }

    /**
     * @param string $name
     *
     * @return Pile
     */
    public static function getOrCreate($name)
    {
        $app = Application::getFacadeApplication();
        $db = Loader::db();
        $u = $app->make(User::class);
        $v = array($name, $u->getUserID());
        $q = "select pID from Piles where name = ? and uID = ?";
        $pID = $db->getOne($q, $v);

        if ($pID > 0) {
            return self::get($pID);
        }

        $v = array($u->getUserID(), 0, $name, 'READY');
        $q = "insert into Piles (uID, isDefault, name, state) values (?, ?, ?, ?)";
        $r = $db->query($q, $v);
        if ($r) {
            $pID = $db->Insert_ID();

            return self::get($pID);
        }
    }

    /**
     * @param Collection|Block $obj
     *
     * @return bool
     */
    public function inPile($obj)
    {
        return $this->getPileContentID($obj) !== null;
    }

    /**
     * @return int
     */
    public function getPileID()
    {
        return $this->pID;
    }

    /**
     * @return Pile
     */
    public static function getDefault()
    {
        $app = Application::getFacadeApplication();
        $db = Loader::db();
        // checks to see if we're registered, or if we're a visitor. Either way, we get a pile entry
        $u = $app->make(User::class);
        if ($u->isRegistered()) {
            $v = array($u->getUserID(), 1);
            $q = "select pID from Piles where uID = ? and isDefault = ?";
        }
        $pID = $db->getOne($q, $v);
        if ($pID > 0) {
            $p = self::get($pID);

            return $p;
        } else {
            // create a new one
            $p = self::createDefaultPile();

            return $p;
        }
    }

    /**
     * @return Pile
     */
    public static function createDefaultPile()
    {
        $app = Application::getFacadeApplication();
        $db = Loader::db();
        // for the sake of data integrity, we're going to ensure that a general pile does not exist
        $u = $app->make(User::class);
        if ($u->isRegistered()) {
            $v = array($u->getUserID(), 1);
            $q = "select pID from Piles where uID = ? and isDefault = ?";
        }
        $pID = $db->getOne($q, $v);
        if ($pID > 0) {
            $p = new self($pID);

            return $p;
        } else {
            // create a new one
            $v = array($u->getUserID(), 1, null, 'READY');
            $q = "insert into Piles (uID, isDefault, name, state) values (?, ?, ?, ?)";
            $r = $db->query($q, $v);
            if ($r) {
                $pID = $db->Insert_ID();

                return self::get($pID);
            }
        }
    }

    /**
     * @return array
     */
    public static function getMyPiles()
    {
        $app = Application::getFacadeApplication();
        $db = Loader::db();

        $u = $app->make(User::class);
        if ($u->isRegistered()) {
            $v = array($u->getUserID());
            $q = "select pID from Piles where uID = ? order by name asc";
        }

        $piles = array();
        $r = $db->query($q, $v);
        if ($r) {
            while ($row = $r->fetch()) {
                $piles[] = self::get($row['pID']);
            }
        }

        return $piles;
    }

    /**
     * @return bool
     */
    public function isMyPile()
    {
        $app = Application::getFacadeApplication();
        $u = $app->make(User::class);

        if ($u->isRegistered()) {
            return $this->getUserID() == $u->getUserID();
        }
    }

    /**
     * @return int
     */
    public function getUserID()
    {
        return $this->uID;
    }

    /**
     * Delete a pile.
     *
     * @return bool
     */
    public function delete()
    {
        $db = Loader::db();
        $v = array($this->pID);
        $q = "delete from Piles where pID = ?";
        $db->query($q, $v);
        $q2 = "delete from PileContents where pID = ?";
        $db->query($q, $v);

        return true;
    }

    /**
     * @return int
     */
    public function getPileLength()
    {
        $db = Loader::db();
        $q = "select count(pcID) from PileContents where pID = ?";
        $v = array($this->pID);
        $r = $db->getOne($q, $v);
        if ($r > 0) {
            return $r;
        } else {
            return 0;
        }
    }

    /**
     * @param string $display
     *
     * @return array
     */
    public function getPileContentObjects($display = 'display_order')
    {
        $pc = array();
        $db = Loader::db();
        switch ($display) {
            case 'display_order_date':
                $order = 'displayOrder asc, timestamp desc';
                break;
            case 'date_desc':
                $order = 'timestamp desc';
                break;
            default:
                $order = 'displayOrder asc';
                break;
        }

        $v = array($this->pID);
        $q = "select pcID from PileContents where pID = ? order by {$order}";
        $r = $db->query($q, $v);
        while ($row = $r->fetch()) {
            $pc[] = PileContent::get($row['pcID']);
        }

        return $pc;
    }

    /**
     * @param Page|Block|PileContent $obj
     * @param int                    $quantity
     *
     * @return mixed
     */
    public function add(&$obj, $quantity = 1)
    {
        $db = Loader::db();
        $existingPCID = $this->getPileContentID($obj);
        $v1 = array($this->pID);
        $q1 = "select max(displayOrder) as displayOrder from PileContents where pID = ?";
        $currentDO = $db->getOne($q1, $v1);
        $displayOrder = $currentDO + 1;
        if (!$existingPCID) {
            $v = array($this->pID, $obj->getBlockID(), "BLOCK", $quantity, $displayOrder);
            $q = "insert into PileContents (pID, itemID, itemType, quantity, displayOrder) values (?, ?, ?, ?, ?)";
            $r = $db->query($q, $v);
            if ($r) {
                $pcID = $db->Insert_ID();

                return $pcID;
            }
        } else {
            return $existingPCID;
        }
    }

    /**
     * @param Collection|Block|PileContent $obj
     *
     * @return int|null
     */
    public function getPileContentID(&$obj)
    {
        $item = $this->getItemID($obj);
        if ($item === null) {
            return null;
        }
        $db = Loader::db();
        $q = "select pcID from PileContents where pID = ? and itemType = ? and itemID = ?";
        $pcID = $db->getOne($q, array($this->pID, $item[0], $item[1]));

        return $pcID > 0 ? (int) $pcID : null;
    }

    /**
     * @param Page|Block|PileContent $obj
     * @param int                    $quantity
     */
    public function remove(&$obj, $quantity = 1)
    {
        $item = $this->getItemID($obj);
        if ($item === null) {
            return;
        }
        $db = Loader::db();
        $v = array($this->pID, $item[0], $item[1]);
        $q = "select quantity from PileContents where pID = ? and itemType = ? and itemID = ?";
        $exQuantity = $db->getOne($q, $v);
        if ($exQuantity > $quantity) {
            $db->query(
               "update PileContents set quantity = quantity - {$quantity} where pID = ? and itemType = ? and itemID = ?",
               $v);
        } else {
            $db->query("delete from PileContents where pID = ? and itemType = ? and itemID = ?", $v);
        }
    }

    /**
     * Get the type and the ID of an item of the pile.
     *
     * @param Collection|Block|PileContent $obj
     *
     * @return array{0: string, 1: int}|null NULL if $obj is not a supported item
     */
    private function getItemID($obj): ?array
    {
        if ($obj instanceof Collection) {
            return ['COLLECTION', (int) $obj->getCollectionID()];
        }
        if ($obj instanceof Block) {
            return ['BLOCK', (int) $obj->getBlockID()];
        }
        if ($obj instanceof PileContent) {
            return [(string) $obj->getItemType(), (int) $obj->getItemID()];
        }

        return null;
    }

    /**
     * @return bool
     */
    public function rescanDisplayOrder()
    {
        $db = Loader::db();
        $v = array($this->pID);
        $q = "select pcID from PileContents where pID = ? order by displayOrder asc";
        $r = $db->query($q, $v);
        $currentDisplayOrder = 0;
        while ($row = $r->fetch()) {
            $v1 = array($currentDisplayOrder, $row['pcID']);
            $q1 = "update PileContents set displayOrder = ? where pcID = ?";
            $db->query($q1, $v1);
            ++$currentDisplayOrder;
        }

        return true;
    }
}
