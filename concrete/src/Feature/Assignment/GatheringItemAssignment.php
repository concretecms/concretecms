<?php
namespace Concrete\Core\Feature\Assignment;

use Concrete\Core\Feature\Feature;
use Concrete\Core\Feature\Category\Category as FeatureCategory;
use Concrete\Core\Feature\Detail\Detail as FeatureDetail;

class GatheringItemAssignment extends Assignment
{
    protected $gaiID;
    public function loadDetails($mixed)
    {
        $this->gaiID = $mixed->getGatheringItemID();
    }

    public static function add(Feature $fe, FeatureDetail $fd, $item)
    {
        $fc = FeatureCategory::getByHandle('gathering_item');
        $fa = parent::addAssignment($fe, $fc, $fd, $item);
        $db = Loader::db();
        $db->Execute('insert into GatheringItemFeatureAssignments (gaiID, faID) values (?, ?)', array(
            $item->getGatheringItemID(),
            $fa->getFeatureAssignmentID(),
        ));

        return $fa;
    }

    public static function getList($item)
    {
        $db = Loader::db();
        $r = $db->Execute('select faID from GatheringItemFeatureAssignments where gaiID = ?', array(
            $item->getGatheringItemID(),
        ));
        $list = array();
        while ($row = $r->FetchRow()) {
            $fa = Assignment::getByID($row['faID'], $item);
            if (is_object($fa)) {
                $list[] = $fa;
            }
        }

        return $list;
    }

    public static function getFeature($feHandle, $item)
    {
        $db = Loader::db();
        $faID = $db->GetOne('select ca.faID from GatheringItemFeatureAssignments as inner join FeatureAssignments fa on as.faID = fa.faID inner join Features fe on fa.feID = fe.feID where gaiID = ? and fe.feHandle = ?', array(
            $item->getGatheringItemID(), $feHandle,
        ));
        if ($faID && $faID > 0) {
            $fa = Assignment::getByID($faID, $item);
            if (is_object($fa)) {
                return $fa;
            }
        }
    }

    public function delete()
    {
        $db = Loader::db();
        $db->Execute('delete from GatheringItemFeatureAssignments where faID = ? and gaiID = ?', array($this->getFeatureAssignmentID(), $this->gaiID));
        if (!$this->assignmentIsInUse()) {
            parent::delete();
        }
    }
}
