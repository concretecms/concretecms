<?php
namespace Concrete\Core\StyleCustomizer;
use Less_Parser;
use Less_Tree_Call;
use \Concrete\Core\StyleCustomizer\Style\ColorStyle;
class Preset {


    protected $filename;
    protected $name;
    const PRESET_RULE_NAME = '@preset-name';
    const PRESET_RULE_ICON = '@preset-icon';
    const PRESET_RULE_ICON_FUNCTION = 'concrete-icon';
    protected $styleValueList;

    /**
     * @return Preset
     */
    public static function getFromFile($lessFile, $urlroot) {
        $o = new static();
        $o->file = $lessFile;
        $o->urlroot = $urlroot;
        $o->filename = basename($lessFile);
        $o->handle = substr($o->filename, 0, strrpos($o->filename, '.'));

        $l = new Less_Parser();
        $parser = $l->parseFile($lessFile, false, true);
        $rules = $parser->rules;
        foreach($rules as $rule) {
            if ($rule->name == static::PRESET_RULE_NAME) {
                $o->name = $rule->value->value[0]->value[0]->value;
            }
            if ($rule->name == static::PRESET_RULE_ICON) {
                $method = $rule->value->value[0]->value[0];
                if ($method instanceof Less_Tree_Call && $method->name == static::PRESET_RULE_ICON_FUNCTION) {
                    $cv1 = ColorStyle::parse($method->args[0]->value[0]);
                    $cv2 = ColorStyle::parse($method->args[1]->value[0]);
                    $cv3 = ColorStyle::parse($method->args[2]->value[0]);
                    $o->color1 = $cv1;
                    $o->color2 = $cv2;
                    $o->color3 = $cv3;
                }
            }
        }

        return $o;
    }

    /**
     * Gets the style value list object for this preset.
     * @return \Concrete\Core\StyleCustomizer\Style\ValueList
     */
    public function getStyleValueList()
    {
        if (!isset($this->styleValueList)) {
            $this->styleValueList = \Concrete\Core\StyleCustomizer\Style\ValueList::loadFromLessFile($this->file, $this->urlroot);
        }
        return $this->styleValueList;
    }

    public function getPresetFilename()
    {
        return $this->filename;
    }

    public function getPresetName()
    {
        return $this->name;
    }

    /** Returns the display name for this preset (localized and escaped accordingly to $format)
     * @param string $format = 'html'
     *   Escape the result in html format (if $format is 'html').
     *   If $format is 'text' or any other value, the display name won't be escaped.
     * @return string
     */
    public function getPresetDisplayName($format = 'html') {
        $value = tc('PresetName', $this->getPresetName());
        switch($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
    }

    public function isDefaultPreset()
    {
        return $this->filename == FILENAME_STYLE_CUSTOMIZER_DEFAULT_PRESET_NAME;
    }

    public function getPresetHandle()
    {
        return $this->handle;
    }

    public function getPresetColor1()
    {
        return $this->color1;
    }

    public function getPresetColor2()
    {
        return $this->color2;
    }

    public function getPresetColor3()
    {
        return $this->color3;
    }

    public function getPresetIconHTML()
    {
        $html = '<ul class="ccm-style-preset-icon">';
        $html .= '<li style="background-color: ' . $this->getPresetColor1()->toStyleString() . '"></li>';
        $html .= '<li style="background-color: ' . $this->getPresetColor2()->toStyleString() . '"></li>';
        $html .= '<li style="background-color: ' . $this->getPresetColor3()->toStyleString() . '"></li>';
        $html .= '</ul>';
        return $html;
    }
}