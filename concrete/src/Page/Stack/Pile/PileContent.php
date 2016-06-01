<?php
namespace Concrete\Core\Page\Stack\Pile;

use Loader;
use Concrete\Core\Foundation\Object;
use Block;

class PileContent extends Object
{
    public $p, $pID, $pcID, $itemID, $itemType, $quantity, $timestamp, $displayOrder;

    public function getPile()
    {
        return $this->p;
    }
    public function getPileContentID()
    {
        return $this->pcID;
    }
    public function getItemID()
    {
        return $this->itemID;
    }
    public function getItemType()
    {
        return $this->itemType;
    }
    public function getQuantity()
    {
        return $this->quantity;
    }

    public function delete()
    {

        // it's assumed that we've already checked whether this user has access to this pile content object

        $db = Loader::db();
        $v = array($this->pcID);
        $q = "delete from PileContents where pcID = ?";
        $r = $db->query($q, $v);
        if ($r) {
            $this->p->rescanDisplayOrder();

            return true;
        }
    }

    public function moveUp()
    {
        $db = Loader::db();
        $this->p->rescanDisplayOrder();
        // now that we know everything is cool regarding display order
        $q = "select displayOrder from PileContents where pcID = {$this->pcID}";
        $displayOrder = $db->getOne($q);
        if ($displayOrder > 0) {
            // we have room to move up

            $targetDO = $displayOrder - 1;
            $q = "select pcID from PileContents where displayOrder = {$targetDO}";

            $pcID = $db->getOne($q);
            $q = "update PileContents set displayOrder = {$targetDO} where pcID = {$this->pcID}";
            $db->query($q);

            $q = "update PileContents set displayOrder = {$displayOrder} where pcID = {$pcID}";
            $db->query($q);
        }
    }

    public function moveDown()
    {
        $db = Loader::db();
        $this->p->rescanDisplayOrder();
        // now that we know everything is cool regarding display order
        $q = "select max(displayOrder) as displayOrder from PileContents where pID = {$this->pID}";
        $maxDisplayOrder = $db->getOne($q);

        $q2 = "select displayOrder from PileContents where pcID = {$this->pcID}";
        $displayOrder = $db->getOne($q2);
        if ($displayOrder < $maxDisplayOrder) {
            // we have room to move up

            $targetDO = $displayOrder + 1;
            $q = "select pcID from PileContents where displayOrder = {$targetDO}";
            $pcID = $db->getOne($q);
            $q = "update PileContents set displayOrder = {$targetDO} where pcID = {$this->pcID}";
            $db->query($q);

            $q = "update PileContents set displayOrder = {$displayOrder} where pcID = {$pcID}";
            $db->query($q);
        }
    }

    public function get($pcID)
    {
        $db = Loader::db();
        $v = array($pcID);
        $q = "select pID, pcID, itemID, itemType, displayOrder, quantity, timestamp from PileContents where pcID = ?";
        $r = $db->query($q, $v);
        $row = $r->fetchRow();

        $pc = new self();
        if (is_array($row)) {
            foreach ($row as $k => $v) {
                $pc->{$k} = $v;
            }
        }

        $p = Pile::get($pc->pID);
        $pc->p = $p; // pc-p . get it ?
        return $pc;
    }

    public function getObject()
    {
        switch ($this->getItemType()) {
            case "COLLECTION":
                $obj = Page::getByID($this->getItemID(), "ACTIVE");
                break;
            case "BLOCK":
                $obj = Block::getByID($this->getItemID());
                break;
        }

        return $obj;
    }

    public function getModuleList()
    {
        $modules = explode(',', PILE_MODULES_INSTALLED);

        return $modules;
    }
}
