<?php
namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Style\Value\SizeValue;
use Less_Tree_Dimension;

class SizeStyle extends Style
{
    public function render($value = false)
    {
        $r = \Concrete\Core\Http\ResponseAssetGroup::get();
        $r->requireAsset('core/style-customizer');

        $strOptions = '';
        $i = 0;
        if (is_object($value)) {
            $options['unit'] = $value->getUnit();
            $options['value'] = $value->getSize();
        }
        $options['inputName'] = $this->getVariable();
        $strOptions = json_encode($options);
        echo '<span class="ccm-style-customizer-display-swatch-wrapper" data-size-selector="' . $this->getVariable() . '"></span>';
        echo "<script type=\"text/javascript\">";
        echo "$(function() { $('span[data-size-selector=" . $this->getVariable() . "]').concreteSizeSelector({$strOptions}); });";
        echo "</script>";
    }

    public function getValueFromRequest(\Symfony\Component\HttpFoundation\ParameterBag $request)
    {
        $size = $request->get($this->getVariable());
        $sv = new SizeValue($this->getVariable());
        $sv->setSize($size['size']);
        $sv->setUnit($size['unit']);

        return $sv;
    }

    public static function parse($value, $variable = false)
    {
        if ($value instanceof Less_Tree_Dimension) {
            $unit = 'px';
            if (isset($value->unit->numerator[0])) {
                $unit = $value->unit->numerator[0];
            }
            $sv = new SizeValue($variable);
            $sv->setSize($value->value);
            $sv->setUnit($unit);
        }

        return $sv;
    }

    public static function getValuesFromVariables($rules = [])
    {
        $values = [];
        foreach ($rules as $rule) {
            if (preg_match('/@(.+)\-size/i',  isset($rule->name) ? $rule->name : '', $matches)) {
                $value = $rule->value->value[0]->value[0];
                $sv = static::parse($value, $matches[1]);
                if (is_object($sv)) {
                    $values[] = $sv;
                }
            }
        }

        return $values;
    }
}
