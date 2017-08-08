<?php
namespace Concrete\Core\Feature\Assignment;

use Concrete\Core\Foundation\ConcreteObject;
use Core;
use Concrete\Core\Feature\Feature;
use Concrete\Core\Feature\Category\Category as FeatureCategory;
use Concrete\Core\Feature\Detail\Detail as FeatureDetail;
use Database;
use RuntimeException;

abstract class Assignment extends ConcreteObject
{
    abstract public function loadDetails($mixed);

    /**
     * @param mixed $mixed
     *
     * @return Assignment[]
     * @abstract
     */
    public static function getList($mixed)
    {
        throw new RuntimeException('This method has not yet been implemented.');
    }

    /**
     * @param string $feHandle
     * @param mixes $mixed
     *
     * @return Assignment|null
     * @abstract
     */
    public static function getFeature($feHandle, $mixed)
    {
        throw new RuntimeException('This method has not yet been implemented.');
    }

    /**
     * @param Feature $fe
     * @param FeatureDetail $fd
     * @param mixed $mixed
     *
     * @return Assignment
     * @abstract
     */
    public static function add(Feature $fe, FeatureDetail $fd, $mixed)
    {
        throw new RuntimeException('This method has not yet been implemented.');
    }

    public static function addAssignment(Feature $fe, FeatureCategory $fc, FeatureDetail $fd, $mixed)
    {
        $db = Database::connection();
        $db->Execute(
            'insert into FeatureAssignments (fcID, feID, fdObject) values (?, ?, ?)',
            array(
                $fc->getFeatureCategoryID(),
                $fe->getFeatureID(),
                serialize($fd),
            )
        );

        $faID = $db->Insert_ID();
        // sometimes feature detail objects need to do more with the feature assignments, so we do that here.
        $fa = static::getByID($faID, $mixed);
        $fd->handleFeatureAssignment($fa);

        return $fa;
    }

    protected function assignmentIsInUse()
    {
        $categories = FeatureCategory::getList();
        foreach ($categories as $cat) {
            if ($cat->assignmentIsInUse($this)) {
                return true;
                break;
            }
        }

        return false;
    }

    public function getFeatureAssignmentID()
    {
        return $this->faID;
    }

    public function getFeatureID()
    {
        return $this->feID;
    }

    public function getFeatureObject()
    {
        return Feature::getByID($this->feID);
    }

    public function getFeatureDetailObject()
    {
        return $this->fdObject;
    }

    public function getFeatureDetailHandle()
    {
        return $this->feHandle;
    }

    public static function getByID($faID, $mixed)
    {
        $db = Database::connection();
        $r = $db->GetRow(
            'select faID, fa.fcID, fdObject, fa.feID, fe.feHandle, fc.fcHandle from FeatureAssignments fa inner join FeatureCategories fc on fa.fcID = fc.fcID inner join Features fe on fa.feID = fe.feID where faID = ?',
            array($faID)
        );
        if (is_array($r) && $r['faID'] == $faID) {
            $class = '\\Concrete\\Core\\Feature\\Assignment\\' . Core::make('helper/text')->camelcase($r['fcHandle']) . 'Assignment';
            $fa = Core::make($class);
            $fa->setPropertiesFromArray($r);
            $fa->fdObject = @unserialize($r['fdObject']);
            $fa->loadDetails($mixed);

            return $fa;
        }
    }

    public function delete()
    {
        $detail = $this->getFeatureDetailObject();
        if (is_object($detail)) {
            $detail->deleteFeatureAssignment($this);
        }
        $db = Database::connection();
        $db->Execute('delete from FeatureAssignments where faID = ?', array($this->getFeatureAssignmentID()));
    }
}
