<?
namespace Concrete\Core\Page\Style;
use \Concrete\Core\Foundation\Object;
use File;
use stdClass;
use Loader;

/**
 * Class CustomStyleRule
 * @property int csrID
 * @property int cspID
 * @property string css_id
 * @property string css_class
 * @property string css_serialized
 * @property string css_custom
 *
 * @package Concrete\Core\Page\Style
 */
class CustomStyleRule extends Object
{

    /**
     * todo: determine if this is ever used
     * @var bool
     */
    protected static $headerStylesAdded = false;

    /**
     * @var string
     */
    protected $customStyleNameSpace = 'customStyle';

    /**
     * @var array
     */
    public static $fontFamilies = array(
        'inherit' => 'inherit',
        'Arial' => "Arial, Helvetica, sans-serif",
        'Times New Roman' => "'Times New Roman', Times, serif",
        'Courier' => "'Courier New', Courier, monospace",
        'Georgia' => "Georgia, 'Times New Roman', Times, serif",
        'Verdana' => "Verdana, Arial, Helvetica, sans-serif"
    );

    /**
     * @param string $ns
     */
    public function setCustomStyleNameSpace($ns)
    {
        $this->customStyleNameSpace = $ns;
    }

    /**
     * @return string
     */
    public function getCssClass()
    {
        return $this->css_class;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->csrID;
    }

    /**
     * @return int
     */
    public function getPresetId()
    {
        return $this->cspID;
    }

    /**
     * @param bool $withAutoID
     * @return string
     */
    public function getCssId($withAutoID = false)
    {
        if (strlen(trim($this->css_id)) > 0) {
            return $this->css_id;
        } else if ($withAutoID && $this->getId()) {
            return $this->customStyleNameSpace . $this->getId();
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getCssCustom()
    {
        return $this->css_custom;
    }

    /**
     * @return mixed
     */
    public function getCustomStylesArray()
    {
        $styles = unserialize($this->css_serialized);
        if (!is_array($styles)) {
            return array();
        }
        return $styles;
    }

    /**
     * @return string
     */
    public function getText()
    {
        $stylesStr = '';
        $tempStyles = array();
        $styles = $this->getCustomStylesArray();
        foreach ($styles as $key => $val) {
            if (!trim($key)) continue;
            switch ($key) {
                case 'border_position':
                case 'border_color':
                case 'border_style':
                    $tempStyles[$key] = $val;
                    break;

                case 'border_width':
                case 'padding_left':
                case 'padding_top':
                case 'padding_right':
                case 'padding_bottom':
                case 'margin_left':
                case 'margin_top':
                case 'margin_right':
                case 'margin_bottom':
                    if (!strlen(trim($val))) $val = 0;
                    if (strlen(trim($val)) == strlen(intval($val)) && intval($val))
                        $val = intval($val) . 'px';
                    $tempStyles[$key] = $val;
                    break;

                case 'line_height':
                case 'font_size':
                    if (!strlen(trim($val)) || !$val) continue;
                    if (strlen(trim($val)) == strlen(intval($val)) && intval($val))
                        $val = intval($val) . 'px';
                    $stylesStr .= str_replace('_', '-', $key) . ':' . $val . '; ';
                    break;

                case 'font_family':
                    if ($val == 'inherit') continue;
                    $val = self::$fontFamilies[$val];
                    $stylesStr .= str_replace('_', '-', $key) . ':' . $val . '; ';
                    break;
                case 'background_image':
                    if ($val > 0) {
                        /** @var \Concrete\Core\File\File $bf */
                        $bf = File::getByID($val);
                        $stylesStr .= str_replace('_', '-', $key) . ': url(\'' . $bf->getRelativePath() . '\'); ';
                    }
                    break;
                default:
                    if (!strlen(trim($val))) continue;
                    $stylesStr .= str_replace('_', '-', $key) . ':' . $val . '; ';
            }
        }

        //shorthand approach to make the css a little tighter looking
        if ($tempStyles['margin_top'] || $tempStyles['margin_right'] || $tempStyles['margin_bottom'] || $tempStyles['margin_left']) {
            $stylesStr .= 'margin:' . $tempStyles['margin_top'] . ' ' . $tempStyles['margin_right'] . ' ' . $tempStyles['margin_bottom'] . ' ' . $tempStyles['margin_left'] . '; ';
        }

        if ($tempStyles['padding_top'] || $tempStyles['padding_right'] || $tempStyles['padding_bottom'] || $tempStyles['padding_left']) {
            $stylesStr .= 'padding:' . $tempStyles['padding_top'] . ' ' . $tempStyles['padding_right'] . ' ' . $tempStyles['padding_bottom'] . ' ' . $tempStyles['padding_left'] . '; ';
        }

        if ($tempStyles['border_width'] && $tempStyles['border_style'] != 'none') {
            if ($tempStyles['border_position'] != 'full'){
                $borderPos = '-' . $tempStyles['border_position'];
            }else{
                $borderPos = '';
            }
            $stylesStr .= 'border' . $borderPos . ':' . $tempStyles['border_width'] . ' ' . $tempStyles['border_style'] . ' ' . $tempStyles['border_color'] . '; ';
        }

        if (strlen(trim($stylesStr)) === 0 && strlen(trim($this->getCssCustom())) === 0) {
            return '';
        }

        $styleRules = str_replace(array("\n", "\r"), '', $stylesStr . $this->getCssCustom());
        return $styleRules;
    }

    /**
     * @param $id
     * @param $class
     * @param $custom
     * @param $keys
     * @return stdClass
     */
    protected static function sanitize($id, $class, $custom, $keys)
    {
        $id = str_replace(array('"', "'", ';', "<", ">", "#"), '', $id);
        $class = str_replace(array('"', "'", ';', "<", ">", "."), '', $class);
        $custom = str_replace('"', "'", $custom);

        $styleKeys = array('font_family', 'color', 'font_size', 'line_height', 'text_align', 'background_color', 'border_style',
            'border_color', 'border_width', 'border_position', 'margin_top', 'margin_right', 'margin_bottom', 'margin_left',
            'padding_top', 'padding_right', 'padding_bottom', 'padding_left', 'background_image', 'background_repeat');

        $cssDataRaw = array();
        foreach ($styleKeys as $styleKey) {
            $cssDataRaw[$styleKey] = $keys[$styleKey];
        }

        $cssData = serialize($cssDataRaw);

        $obj = new stdClass;
        $obj->id = $id;
        $obj->class = $class;
        $obj->custom = $custom;
        $obj->cssData = $cssData;
        return $obj;
    }

    /**
     * Used to create new CustomStyleRules
     * @param $id
     * @param $class
     * @param $custom
     * @param $keys
     * @return CustomStyleRule|null Returns the newly created CustomStyleRule object, or null if it was unable to be created
     */
    public static function add($id, $class, $custom, $keys)
    {
        $obj = self::sanitize($id, $class, $custom, $keys);
        $db = Loader::db;
        $db->execute('insert into CustomStyleRules (css_id, css_class, css_custom, css_serialized) values (?, ?, ?, ?)', array($obj->id, $obj->class, $obj->custom, $obj->cssData));
        $csrID = $db->Insert_ID();
        return CustomStyleRule::getByID($csrID);
    }

    /**
     * Updates the values of the current object
     * @param string $cssId
     * @param string $cssClass
     * @param string $cssCustom
     * @param string $cssSerialized
     */
    public function update($cssId, $cssClass, $cssCustom, $cssSerialized)
    {
        //todo: update method should probably return the number of records updated or true/false
        $obj = self::sanitize($cssId, $cssClass, $cssCustom, $cssSerialized);
        $db = Loader::db();
        $db->Execute('update CustomStyleRules set css_id = ?, css_class = ?, css_custom = ?, css_serialized = ? where csrID = ?', array($obj->id, $obj->class, $obj->custom, $obj->cssData, $this->getId()));
    }

    /**
     * @param int $csrID
     * @return CustomStyleRule|null Returns the CustomStyleRule if found, or null if no CustomStyleRule exists for this ID
     */
    public static function getByID($csrID)
    {
        $csr = new CustomStyleRule();
        $db = Loader::db();
        $r = $db->GetRow('select CustomStyleRules.*, CustomStylePresets.cspID from CustomStyleRules left join CustomStylePresets on CustomStyleRules.csrID = CustomStylePresets.csrID where CustomStyleRules.csrID = ?', array($csrID));
        if (is_array($r) && $r['csrID'] > 0) {
            $csr->setPropertiesFromArray($r);
        }
        if (is_object($csr) && $csr->getId() == $csrID) {
            return $csr;
        }
        return null;
    }

    /**
     * Below this line is the land of deprecated functions, these were replaced with functions that have a better naming convention
     */

    /**
     * @deprecated replaced by getId function
     * @return int
     */
    public function getCustomStyleRuleID()
    {
        return $this->getId();
    }

    /**
     * @deprecated replaced by getPresetId function
     * @return int
     */
    public function getCustomStylePresetID()
    {
        return $this->getPresetID();
    }

    /**
     * @deprecated replaced by getCssId function
     * @param bool $withAutoID
     * @return string
     */
    public function getCustomStyleRuleCSSID($withAutoID = false)
    {
        $this->getCssId($withAutoID);
    }

    /**
     * @deprecated
     * @return string
     */
    public function getCustomStyleRuleClassName()
    {
        return $this->getCssClass();
    }

    /**
     * @deprecated replaced by getCssCustom function
     * @return string
     */
    public function getCustomStyleRuleCSSCustom()
    {
        return $this->getCssCustom();
    }

    /**
     * @deprecated replaced by getCustomStylesArray function
     * @return mixed
     */
    public function getCustomStyleRuleCustomStylesArray()
    { // worst method name ever
        return $this->getCustomStylesArray();
    }

    /**
     * @deprecated replaced by getTex
     * @return string
     */
    public function getCustomStyleRuleText()
    {
        return $this->getText();
    }
}