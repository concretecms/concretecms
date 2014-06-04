<?php
namespace Concrete\Core\Page\Stack\Pile;

use Concrete\Core\Block\Block;
use Concrete\Core\Foundation\Object;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Page;
use Concrete\Core\User\User;
use Loader;

/**
 * Class Pile
 *
 * Essentially a user's scrapbook, a pile is an object used for clumping bits of content together around a user account.
 * Piles currently only contain blocks but they could also contain collections. Any bit of content inside a user's pile
 * can be reordered, etc... although no public interface makes use of much of this functionality.
 *
 * @package Concrete\Core\Page\Stack\Pile
 *
 */
class Pile extends Object
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
     * @return Pile
     */
    public function create($name)
    {
        $db = Loader::db();
        $u = new User();
        $v = array($u->getUserID(), 0, $name, 'READY');
        $q = "insert into Piles (uID, isDefault, name, state) values (?, ?, ?, ?)";
        $r = $db->query($q, $v);
        if ($r) {
            $pID = $db->Insert_ID();
            return Pile::get($pID);
        }
    }

    /**
     * @param int $pID
     * @return Pile
     */
    public function get($pID)
    {
        $db = Loader::db();
        $v = array($pID);
        $q = "select pID, uID, isDefault, name, state from Piles where pID = ?";
        $r = $db->query($q, $v);
        $row = $r->fetchRow();

        $p = new Pile;
        if (is_array($row)) {
            foreach ($row as $k => $v) {
                $p->{$k} = $v;
            }
        }
        return $p;
    }

    /**
     * @param string $name
     * @return Pile
     */
    public function getOrCreate($name)
    {
        $db = Loader::db();
        $u = new User();
        $v = array($name, $u->getUserID());
        $q = "select pID from Piles where name = ? and uID = ?";
        $pID = $db->getOne($q, $v);

        if ($pID > 0) {
            return Pile::get($pID);
        }

        $v = array($u->getUserID(), 0, $name, 'READY');
        $q = "insert into Piles (uID, isDefault, name, state) values (?, ?, ?, ?)";
        $r = $db->query($q, $v);
        if ($r) {
            $pID = $db->Insert_ID();
            return Pile::get($pID);
        }
    }

    /**
     * @param Collection|Block $obj
     * @return bool
     */
    public function inPile($obj)
    {
        $db = Loader::db();
        $v = array();
        $class = strtoupper(get_class($obj));
        switch ($class) {
            case "COLLECTION":
                $v = array("COLLECTION", $obj->getCollectionID());
                break;
            case "BLOCK":
                $v = array("BLOCK", $obj->getBlockID());
                break;
        }
        $v[] = $this->getPileID();
        $q = "select pcID from PileContents where itemType = ? and itemID = ? and pID = ?";
        $pcID = $db->getOne($q, $v);

        return ($pcID > 0);
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
    public function getDefault()
    {
        $db = Loader::db();
        // checks to see if we're registered, or if we're a visitor. Either way, we get a pile entry
        $u = new User();
        if ($u->isRegistered()) {
            $v = array($u->getUserID(), 1);
            $q = "select pID from Piles where uID = ? and isDefault = ?";
        }
        $pID = $db->getOne($q, $v);
        if ($pID > 0) {
            $p = Pile::get($pID);
            return $p;
        } else {
            // create a new one
            $p = Pile::createDefaultPile();
            return $p;
        }
    }

    /**
     * @return Pile
     */
    public function createDefaultPile()
    {

        $db = Loader::db();
        // for the sake of data integrity, we're going to ensure that a general pile does not exist
        $u = new User();
        if ($u->isRegistered()) {
            $v = array($u->getUserID(), 1);
            $q = "select pID from Piles where uID = ? and isDefault = ?";
        }
        $pID = $db->getOne($q, $v);
        if ($pID > 0) {
            $p = new Pile($pID);
            return $p;
        } else {
            // create a new one
            $v = array($u->getUserID(), 1, null, 'READY');
            $q = "insert into Piles (uID, isDefault, name, state) values (?, ?, ?, ?)";
            $r = $db->query($q, $v);
            if ($r) {
                $pID = $db->Insert_ID();
                return Pile::get($pID);
            }
        }
    }

    /**
     * @return array
     */
    public function getMyPiles()
    {
        $db = Loader::db();

        $u = new User();
        if ($u->isRegistered()) {
            $v = array($u->getUserID());
            $q = "select pID from Piles where uID = ? order by name asc";
        }

        $piles = array();
        $r = $db->query($q, $v);
        if ($r) {
            while ($row = $r->fetchRow()) {
                $piles[] = Pile::get($row['pID']);
            }
        }

        return $piles;
    }

    /**
     * @return bool
     */
    public function isMyPile()
    {
        $u = new User();

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
     * Delete a pile
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
        while ($row = $r->fetchRow()) {
            $pc[] = PileContent::get($row['pcID']);
        }
        return $pc;
    }

    /**
     * @param Page|Block|PileContent $obj
     * @param int                    $quantity
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
     * @param Page $obj
     * @return mixed
     */
    public function getPileContentID(&$obj)
    {
        $db = Loader::db();
        switch (strtolower(get_class($obj))) {
            case "page":
                $v = array($this->pID, $obj->getCollectionID(), "COLLECTION");
                $q = "select pcID from PileContents where pID = ? and itemID = ? and itemType = ?";
                $pcID = $db->getOne($q, $v);
                if ($pcID > 0) {
                    return $pcID;
                }
                break;
        }
    }

    /**
     * @param Page|Block|PileContent $obj
     * @param int                    $quantity
     */
    public function remove(&$obj, $quantity = 1)
    {
        $db = Loader::db();
        switch (strtolower(get_class($obj))) {
            case "page":
                $v = array($this->pID, $obj->getCollectionID(), "COLLECTION");
                break;
            case "block":
                $v = array($this->pID, $obj->getBlockID(), "BLOCK");
                break;
            case "pilecontent":
                $v = array($this->pID, $obj->getItemID(), $obj->getItemType());
                break;
        }

        $q = "select quantity from PileContents where pID = ? and itemID = ? and itemType = ?";
        $exQuantity = $db->getOne($q, $v);
        if ($exQuantity > $quantity) {
            $db->query(
               "update PileContent set quantity = quantity - {$quantity} where pID = ? and itemID = ? and itemType = ?",
               $v);
        } else {
            $db->query("delete from PileContents where pID = ? and itemID = ? and itemType = ?", $v);
        }
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
        while ($row = $r->fetchRow()) {
            $v1 = array($currentDisplayOrder, $row['pcID']);
            $q1 = "update PileContents set displayOrder = ? where pcID = ?";
            $db->query($q1, $v1);
            $currentDisplayOrder++;
        }
        return true;
    }
}

