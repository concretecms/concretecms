<?
namespace Concrete\Core\Page\Style;

use Concrete\Core\Package\Package;
use Loader;
use \Concrete\Core\Foundation\Object;

/**
 * Class CustomStylePreset
 * @property int $cspID
 * @property string $cspName
 * @property int $csrID
 * @package Concrete\Core\Page\Style
 */
class CustomStylePreset extends Object
{
    /**
     * @var null|CustomStyleRule Used for caching a CustomStyleRule that has already been looked up to reduce database calls
     */
    protected $csr = null;

    /**
     * @return CustomStylePreset[]
     */
    public function getList()
    {
        $db = Loader::db();
        $r = $db->Execute('select cspID, cspName, csrID from CustomStylePresets order by cspName asc');
        $presets = array();
        while ($row = $r->FetchRow()) {
            $obj = new CustomStylePreset();
            $obj->setPropertiesFromArray($row);
            $presets[] = $obj;
        }
        return $presets;
    }

    /**
     * @return int the Custom Style Preset ID
     */
    public function getId()
    {
        return $this->cspID;
    }

    /**
     * @return string the Custom Style Preset Name
     */
    public function getName()
    {
        return $this->cspName;
    }

    /**
     * @return int the Custom Style Rule ID
     */
    public function getCsrId()
    {
        return $this->csrID;
    }

    /**
     * @param bool $requery If set to true, this will force a database lookup for the CustomStyleRule
     * @return CustomStyleRule|null
     */
    public function getCustomStyleRule($requery = false)
    {
        if($requery || $this->csr === null){
            $this->csr = CustomStyleRule::getByID($this->csrID);
        }
        return $this->csr;
    }

    /**
     * @param int $cspID
     * @return CustomStylePreset|null
     * This function will retrieve the CustomStylePreset object for the given cspID, or null if no record could be found.
     */
    public static function getByID($cspID)
    {
        $csp = new CustomStylePreset();
        $db = Loader::db();
        $r = $db->GetRow('select cspID, cspName, csrID from CustomStylePresets where cspID  = ?', array($cspID));
        if (is_array($r) && $r['cspID'] > 0) {
            $csp->setPropertiesFromArray($r);
            if ($csp->getId() == $cspID) {
                return $csp;
            }
        }
        return null;
    }

    /**
     * Removes the current Custom Style Preset. Does NOT remove the associated Custom Style Rule.
     *
     * todo: this should probably return true/false
     */
    public function delete()
    {
        $db = Loader::db();
        $db->Execute('delete from CustomStylePresets where cspID = ?', array($this->cspID));
    }

    /**
     * This function creates a CustomStylePresets record for the given CustomStyleRule and
     * @param string $cspName
     * @param CustomStyleRule $csr
     */
    public static function add($cspName, $csr)
    {
        //todo: make sure that the csrId actually exists before we go ahead and insert this record
        $db = Loader::db();
        $db->Execute('insert into CustomStylePresets (cspName, csrID) values (?, ?)', array(
            $cspName,
            $csr->getId()
        ));

    }

    /**
     * Deprecated functions for backward comparability below this line
     */

    /**
     * @deprecated replaced by getId function
     * @return int
     */
    public function getCustomStylePresetID()
    {
        return $this->getId();
    }

    /**
     * @deprecated replaced by getName function
     * @return string
     */
    public function getCustomStylePresetName()
    {
        return $this->getName();
    }

    /**
     * @deprecated replaced by getCsrId function
     * @return int
     */
    public function getCustomStylePresetRuleID()
    {
        return $this->getCsrId();
    }

    /**
     * @deprecated replaced by getCustomStyleRule function
     * @return CustomStyleRule|null
     */
    public function getCustomStylePresetRuleObject()
    {
        return $this->getCustomStyleRule();
    }

}