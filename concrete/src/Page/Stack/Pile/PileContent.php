<?php
namespace Concrete\Core\Page\Stack\Pile;

use Concrete\Core\Application\Service\Urls;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Support\Facade\Application;
use Loader;
use Concrete\Core\Foundation\ConcreteObject;
use Concrete\Core\Block\Block;

class PileContent extends ConcreteObject implements \JsonSerializable
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

    public static function get($pcID)
    {
        $db = Loader::db();
        $v = array($pcID);
        $q = "select pID, pcID, itemID, itemType, displayOrder, quantity, timestamp from PileContents where pcID = ?";
        $r = $db->query($q, $v);
        $row = $r->fetch();

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

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $block = Block::getByID($this->getItemID());

        if (!$block || !is_object($block) || $block->isError()) {
            return [
                "name" => t('(Deleted Block)'),
                "pileContentId" => $this->getPileContentID(),
            ];
        }

        /** @var \Concrete\Core\Entity\Block\BlockType\BlockType $type */
        $type = $block->getBlockTypeObject();
        $app = Application::getFacadeApplication();
        /** @var Urls $ci */
        $ci = $app->make(Urls::class);

        $icon = $ci->getBlockTypeIconURL($type);

        ob_start();
        $bv = new BlockView($block);
        $bv->render('scrapbook');
        $blockContent = ob_get_contents();
        ob_end_clean();

        return [
            "name" => $type->getBlockTypeName(),
            "handle" => $type->getBlockTypeHandle(),
            "dialogTitle" => t('Add %s', t($type->getBlockTypeName())),
            "dialogWidth" => (int)$type->getBlockTypeInterfaceWidth(),
            "dialogHeight" => (int)$type->getBlockTypeInterfaceHeight(),
            "hasAddTemplate" => (int)$type->hasAddTemplate(),
            "supportsInlineAdd" => (int)$type->supportsInlineAdd(),
            "blockTypeId" => $type->getBlockTypeID(),
            "pileContentId" => $this->getPileContentID(),
            "draggingAvatar" => h('<div class="ccm-block-icon-wrapper d-flex align-items-center justify-content-center"><img src="' . $icon . '" /></div><p><span>' . t($type->getBlockTypeName()) . '</span></p>'),
            "blockId" => (int) $block->getBlockID(),
            "blockContent" => $blockContent
        ];
    }
}
