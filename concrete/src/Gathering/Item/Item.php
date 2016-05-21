<?php
namespace Concrete\Core\Gathering\Item;

use Core;
use Database;
use View;
use Concrete\Core\Foundation\Object;
use Concrete\Core\Gathering\DataSource\DataSource as GatheringDataSource;
use RuntimeException;

/**
 * Base class for gathering items.
 *
 * @method static Key[] add(Gathering $ag, GatheringDataSource $ags, string $gaiPublicDateTime, string $gaiTitle, string $gaiKey, int $gaiSlotWidth = 1, int $gaiSlotHeight = 1) Deprecated method. Use Item::create instead.
 */
abstract class Item extends Object
{
    abstract public function loadDetails();

    abstract public function canViewGatheringItem();

    abstract public function assignFeatureAssignments($mixed);

    /**
     * @param mixed $mixed
     *
     * @abstract
     */
    public static function getListByItem($mixed)
    {
        throw new RuntimeException('This method has not yet been implemented.');
    }

    protected $feHandles;
    protected $templates;

    public function getGatheringItemID()
    {
        return $this->gaiID;
    }

    public function getGatheringDataSourceHandle()
    {
        return $this->gasHandle;
    }

    public function getGatheringDataSourceID()
    {
        return $this->gasID;
    }

    public function getGatheringItemPublicDateTime()
    {
        return $this->gaiPublicDateTime;
    }

    public function getGatheringItemTemplateID(GatheringItemTemplateType $type)
    {
        if (!isset($this->templates)) {
            $this->loadGatheringItemTemplates();
        }

        return $this->templates[$type->getGatheringItemTemplateTypeID()];
    }

    public function getGatheringItemTemplateObject(GatheringItemTemplateType $type)
    {
        $gatID = $this->getGatheringItemTemplateID($type);
        if ($gatID) {
            return GatheringItemTemplate::getByID($gatID);
        }
    }

    public function getGatheringItemTemplateHandle()
    {
        return $this->gatHandle;
    }

    public function getGatheringItemSlotWidth()
    {
        return $this->gaiSlotWidth;
    }

    public function getGatheringItemSlotHeight()
    {
        return $this->gaiSlotHeight;
    }

    public function getGatheringItemBatchTimestamp()
    {
        return $this->gaiBatchTimestamp;
    }

    public function getGatheringItemBatchDisplayOrder()
    {
        return $this->gaiBatchDisplayOrder;
    }

    public function getGatheringItemKey()
    {
        return $this->gaiKey;
    }

    public function getGatheringObject()
    {
        return Gathering::getByID($this->gaID);
    }

    public function getGatheringID()
    {
        return $this->gaID;
    }

    public function getGatheringItemFeatureHandles()
    {
        if (!isset($this->feHandles)) {
            $db = Database::connection();
            $this->feHandles = $db->GetCol(
                'select distinct feHandle from GatheringItemFeatureAssignments afa inner join FeatureAssignments fa on afa.faID = fa.faID inner join Features fe on fa.feID = fe.feID where gaiID = ?',
                array($this->gaiID)
            );
        }

        return $this->feHandles;
    }

    protected function loadGatheringItemTemplates()
    {
        $this->templates = array();
        $db = Database::connection();
        $r = $db->Execute(
            'select gatID, gatTypeID from GatheringItemSelectedTemplates where gaiID = ?',
            array($this->gaiID)
        );
        while ($row = $r->FetchRow()) {
            $this->templates[$row['gatTypeID']] = $row['gatID'];
        }
    }

    public function moveToNewGathering(Gathering $gathering)
    {
        $db = Database::connection();
        $db->Execute(
            'update GatheringItems set gaID = ? where gaiID = ?',
            array($gathering->getGatheringID(), $this->gaiID)
        );
        $this->gaID = $gathering->getGatheringID();
        $batch = $db->GetOne('select max(gaiBatchTimestamp) from GatheringItems where gaiID = ?', array($this->gaiID));
        $this->setGatheringItemBatchTimestamp($batch);
        $this->setGatheringItemBatchDisplayOrder(0);
    }

    public function setGatheringItemTemplate(GatheringItemTemplateType $type, GatheringItemTemplate $template)
    {
        $db = Database::connection();
        $db->Execute(
            'delete from GatheringItemSelectedTemplates where gaiID = ? and gatTypeID = ?',
            array($this->gaiID, $type->getGatheringItemTemplateTypeID())
        );
        $db->Execute(
            'insert into GatheringItemSelectedTemplates (gatTypeID, gaiID, gatID) values (?, ?, ?)',
            array(
                $type->getGatheringItemTemplateTypeID(),
                $this->gaiID,
                $template->getGatheringItemTemplateID(),
            )
        );
        $this->loadGatheringItemTemplates();
    }

    public function setGatheringItemBatchDisplayOrder($gaiBatchDisplayOrder)
    {
        $db = Database::connection();
        $db->Execute(
            'update GatheringItems set gaiBatchDisplayOrder = ? where gaiID = ?',
            array($gaiBatchDisplayOrder, $this->gaiID)
        );
        $this->gaiBatchDisplayOrder = $gaiBatchDisplayOrder;
    }

    public function setGatheringItemBatchTimestamp($gaiBatchTimestamp)
    {
        $db = Database::connection();
        $db->Execute(
            'update GatheringItems set gaiBatchTimestamp = ? where gaiID = ?',
            array($gaiBatchTimestamp, $this->gaiID)
        );
        $this->gaiBatchTimestamp = $gaiBatchTimestamp;
    }

    public function setGatheringItemSlotWidth($gaiSlotWidth)
    {
        $db = Database::connection();
        $db->Execute('update GatheringItems set gaiSlotWidth = ? where gaiID = ?', array($gaiSlotWidth, $this->gaiID));
        $this->gaiSlotWidth = $gaiSlotWidth;
    }

    public function setGatheringItemSlotHeight($gaiSlotHeight)
    {
        $db = Database::connection();
        $db->Execute(
            'update GatheringItems set gaiSlotHeight = ? where gaiID = ?',
            array($gaiSlotHeight, $this->gaiID)
        );
        $this->gaiSlotHeight = $gaiSlotHeight;
    }

    public static function getByID($gaiID)
    {
        $db = Database::connection();
        $r = $db->GetRow(
            'select GatheringItems.*, GatheringDataSources.gasHandle from GatheringItems inner join GatheringDataSources on GatheringItems.gasID = GatheringDataSources.gasID where gaiID = ?',
            array($gaiID)
        );
        if (is_array($r) && $r['gaiID'] == $gaiID) {
            if (!$r['gaiIsDeleted']) {
                $class = Core::make('helper/text')->camelcase($r['gasHandle']) . 'GatheringItem';
                $ags = new $class();
                $ags->setPropertiesFromArray($r);
                $ags->loadDetails();

                return $ags;
            }
        }
    }

    protected static function getListByKey(GatheringDataSource $ags, $gaiKey)
    {
        $db = Database::connection();
        $r = $db->Execute(
            'select gaiID from GatheringItems where gasID = ? and gaiKey = ?',
            array(
                $ags->getGatheringDataSourceID(),
                $gaiKey,
            )
        );
        $items = array();
        while ($row = $r->FetchRow()) {
            $item = GatheringItem::getByID($row['gaiID']);
            if (is_object($item)) {
                $items[] = $item;
            }
        }

        return $items;
    }

    public static function __callStatic($name, $arguments)
    {
        if (strcasecmp($name, 'add') === 0) {
            return call_user_func_array('static::create', $arguments);
        }
        trigger_error("Call to undefined method ".__CLASS__."::$name()", E_USER_ERROR);
    }

    public static function create(
        Gathering $ag,
        GatheringDataSource $ags,
        $gaiPublicDateTime,
        $gaiTitle,
        $gaiKey,
        $gaiSlotWidth = 1,
        $gaiSlotHeight = 1
    ) {
        $db = Database::connection();
        $gaiDateTimeCreated = Core::make('helper/date')->getOverridableNow();
        $r = $db->Execute(
            'insert into GatheringItems (gaID, gasID, gaiDateTimeCreated, gaiPublicDateTime, gaiTitle, gaiKey, gaiSlotWidth, gaiSlotHeight) values (?, ?, ?, ?, ?, ?, ?, ?)',
            array(
                $ag->getGatheringID(),
                $ags->getGatheringDataSourceID(),
                $gaiDateTimeCreated,
                $gaiPublicDateTime,
                $gaiTitle,
                $gaiKey,
                $gaiSlotWidth,
                $gaiSlotHeight,
            )
        );

        return GatheringItem::getByID($db->Insert_ID());
    }

    public function duplicate(Gathering $gathering)
    {
        $db = Database::connection();
        $gaID = $gathering->getGatheringID();
        $db->Execute(
            'insert into GatheringItems (gaID, gasID, gaiDateTimeCreated, gaiPublicDateTime, gaiTitle, gaiKey, gaiSlotWidth, gaiSlotHeight, gaiBatchTimestamp, gaiBatchDisplayOrder)
                        values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            array(
                $gaID,
                $this->getGatheringDataSourceID(),
                $this->gaiDateTimeCreated,
                $this->gaiPublicDateTime,
                $this->gaiTitle,
                $this->gaiKey,
                $this->gaiSlotWidth,
                $this->gaiSlotHeight,
                $this->gaiBatchTimestamp,
                $this->gaiBatchDisplayOrder,
            )
        );

        $this->loadGatheringItemTemplates();
        $gaiID = $db->Insert_ID();

        foreach ($this->templates as $gatTypeID => $gatID) {
            $db->Execute(
                'insert into GatheringItemSelectedTemplates (gaiID, gatTypeID, gatID) values (?, ?, ?)',
                array($gaiID, $gatTypeID, $gatID)
            );
        }

        $item = GatheringItem::getByID($gaiID);

        $assignments = GatheringItemFeatureAssignment::getList($this);
        foreach ($assignments as $as) {
            $item->copyFeatureAssignment($as);
        }

        return $item;
    }

    public function deleteFeatureAssignments()
    {
        $assignments = GatheringItemFeatureAssignment::getList($this);
        foreach ($assignments as $as) {
            $as->delete();
        }
    }

    public function addFeatureAssignment($feHandle, $mixed)
    {
        $f = Feature::getbyHandle($feHandle);
        $fd = $f->getFeatureDetailObject($mixed);
        $as = GatheringItemFeatureAssignment::add($f, $fd, $this);

        return $as;
    }

    public function copyFeatureAssignment(FeatureAssignment $fa)
    {
        return GatheringItemFeatureAssignment::add($fa->getFeatureObject(), $fa->getFeatureDetailObject(), $this);
    }

    protected function sortByFeatureScore($a, $b)
    {
        $ascore = $a->getGatheringTemplateFeaturesTotalScore();
        $bscore = $b->getGatheringTemplateFeaturesTotalScore();
        if ($ascore > $bscore) {
            return -1;
        } else {
            if ($ascore < $bscore) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    protected function weightByFeatureScore($a, $b)
    {
        $ascore = $a->getGatheringTemplateFeaturesTotalScore();
        $bscore = $b->getGatheringTemplateFeaturesTotalScore();

        return mt_rand(0, ($ascore + $bscore)) > $ascore ? 1 : -1;
    }

    public function setAutomaticGatheringItemSlotWidth()
    {
        $amount = floor(1 + pow(lcg_value(), 1.4) * 4);
        $this->setGatheringItemSlotWidth($amount);
    }

    public function setAutomaticGatheringItemSlotHeight()
    {
        $amount = floor(1 + pow(lcg_value(), 1.4) * 2);
        $this->setGatheringItemSlotHeight($amount);
    }

    public function setAutomaticGatheringItemTemplate()
    {
        $arr = Core::make('helper/arrays');
        $db = Database::connection();
        $myFeatureHandles = $this->getGatheringItemFeatureHandles();

        // we loop through and do it for all installed gathering item template types
        $types = GatheringItemTemplateType::getList();
        foreach ($types as $type) {
            $matched = array();
            $r = $db->Execute(
                'select gatID from GatheringItemTemplates where gatTypeID = ?',
                array($type->getGatheringItemTemplateTypeID())
            );
            while ($row = $r->FetchRow()) {
                $templateFeatureHandles = $db->GetCol(
                    'select feHandle from Features f inner join GatheringItemTemplateFeatures af on f.feID = af.feID where gatID = ?',
                    array($row['gatID'])
                );
                if ($arr->subset($templateFeatureHandles, $myFeatureHandles)) {
                    $matched[] = GatheringItemTemplate::getByID($row['gatID']);
                }
            }

            usort($matched, array($this, 'sortByFeatureScore'));
            if (is_object($matched[0]) && $matched[0]->gatheringItemTemplateIsAlwaysDefault()) {
                $template = $matched[0];
            } else {
                // we do some fun randomization math.
                usort($matched, array($this, 'weightByFeatureScore'));
                $template = $matched[0];
            }
            if (is_object($template)) {
                $this->setGatheringItemTemplate($type, $template);
                if ($template->gatheringItemTemplateControlsSlotDimensions()) {
                    $this->setGatheringItemSlotWidth($template->getGatheringItemTemplateSlotWidth($this));
                    $this->setGatheringItemSlotHeight($template->getGatheringItemTemplateSlotHeight($this));
                }
            }
        }
    }

    public function itemSupportsGatheringItemTemplate(GatheringItemTemplate $template)
    {
        // checks to see if all the features necessary to implement the template are present in this item.
        $templateFeatures = $template->getGatheringItemTemplateFeatureHandles();
        $itemFeatures = $this->getGatheringItemFeatureHandles();
        $features = array_intersect($templateFeatures, $itemFeatures);

        return count($features) == count($templateFeatures);
    }

    public function delete()
    {
        $db = Database::connection();
        $db->Execute('delete from GatheringItems where gaiID = ?', array($this->gaiID));
        $db->Execute('delete from GatheringItemSelectedTemplates where gaiID = ?', array($this->gaiID));
        $this->deleteFeatureAssignments();
    }

    public function deactivate()
    {
        $db = Database::connection();
        $db->Execute('update GatheringItems set gaiIsDeleted = 1 where gaiID = ?', array($this->gaiID));
    }

    public function render(GatheringItemTemplateType $type)
    {
        $t = $this->getGatheringItemTemplateObject($type);
        if (is_object($t)) {
            $data = $t->getGatheringItemTemplateData($this);
            $env = Environment::get();
            extract($data);
            View::element(
                DIRNAME_GATHERING . '/' . DIRNAME_GATHERING_ITEM_TEMPLATES . '/' . $type->getGatheringItemTemplateTypeHandle(
                ) . '/' . $t->getGatheringItemTemplateHandle() . '/view',
                $data
            );
        }
    }
}
