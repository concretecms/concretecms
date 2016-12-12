<?php
namespace Concrete\Core\Area;

use Loader;

class SubArea extends Area
{

    const AREA_SUB_DELIMITER = ' : ';

    /**
     * @var \Block
     */
    protected $parentBlock;

    /**
     * @param \Block $block
     */
    public function setSubAreaBlockObject($block)
    {
        $this->parentBlock = $block;
    }

    /**
     * @param \Page $c
     * @param string $arHandle
     * @return Area
     */
    public function create($c, $arHandle)
    {
        $db = Loader::db();
        $db->Replace('Areas', array('cID' => $c->getCollectionID(), 'arHandle' => $arHandle, 'arParentID' => $this->arParentID), array('arHandle', 'cID'), true);
        $this->refreshCache($c);
        $area = self::get($c, $arHandle);
        $area->rescanAreaPermissionsChain();
        return $area;
    }

    /**
     * @return bool|Area|mixed|null
     */
    public function getSubAreaParentPermissionsObject()
    {
        $cache = \Core::make('cache/request');
        $item = $cache->getItem(sprintf('subarea/parent/permissions/%s', $this->getAreaID()));
        if (!$item->isMiss()) {
            return $item->get();
        }

        $db = Loader::db();
        $arParentID = $this->arParentID;
        if ($arParentID == 0) {
            return false;
        }

        while ($arParentID > 0) {
            $row = $db->GetRow('select arID, arHandle, arParentID, arOverrideCollectionPermissions from Areas where arID = ?', array($arParentID));
            $arParentID = $row['arParentID'];
            if ($row['arOverrideCollectionPermissions']) {
                break;
            }
        }
        $a = Area::get($this->c, $row['arHandle']);
        $item->set($a);
        return $a;
    }

    /**
     * @return \Block
     */
    public function getSubAreaBlockObject()
    {
        return $this->parentBlock;
    }

    /**
     * @param string $arHandle
     * @param string $arParentHandle
     * @param int $arParentID
     */
    public function __construct($arHandle, $arParentHandle, $arParentID)
    {
        $this->arParentID = $arParentID;
        $arHandle = $arParentHandle . self::AREA_SUB_DELIMITER . $arHandle;
        parent::__construct($arHandle);
    }

    /**
     * @return int
     */
    public function getAreaParentID()
    {
        return $this->arParentID;
    }

    public function getAreaCustomTemplates($include_parent_templates=true) {
        $these_templates = parent::getAreaCustomTemplates();

        if ($include_parent_templates && $this->parentBlock && $this->parentBlock->a) {
            // include parent templates if instructed to do so
            $parent_templates = $this->parentBlock->a->getAreaCustomTemplates();

            // make sure that parent templates can be overwritten by
            // custom templates set on the subarea itself
            return array_merge($parent_templates, $these_templates);
        }

        return $these_templates;
    }

    /**
     * @param \SimpleXMLElement $p
     * @param \Page $page
     */
    public function export($p, $page)
    {
        $c = $this->getAreaCollectionObject();
        $style = $c->getAreaCustomStyle($this);
        if (is_object($style)) {
            $set = $style->getStyleSet();
            $set->export($p);
        }
        $blocks = $page->getBlocks($this->getAreaHandle());
        foreach ($blocks as $bl) {
            $bl->export($p);
        }
    }

    public function delete()
    {
        $db = Loader::db();
        $blocks = $this->getAreaBlocksArray();
        foreach ($blocks as $b) {
            $bp = new \Permissions($b);
            if ($bp->canDeleteBlock()) {
                $b->deleteBlock();
            }
        }
        $db->Execute('delete from Areas where arID = ?', array($this->arID));
    }
}