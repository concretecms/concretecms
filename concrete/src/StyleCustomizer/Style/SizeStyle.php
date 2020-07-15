<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Style\Value\SizeValue;
use Less_Tree_Dimension;
use Symfony\Component\HttpFoundation\ParameterBag;

class SizeStyle extends Style
{
    /**
     * @param \Concrete\Core\StyleCustomizer\Style\Value\SizeValue|null|false $value
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Style::render()
     */
    public function render($value = false)
    {
        $r = \Concrete\Core\Http\ResponseAssetGroup::get();
        $r->requireAsset('core/style-customizer');

        $strOptions = '';
        $options = [
            'inputName' => $this->getVariable(),
        ];
        if ($value) {
            $options['unit'] = $value->getUnit();
            $options['value'] = $value->getSize();
        }
        $strOptions = json_encode($options);
        echo <<<EOT
<span class="ccm-style-customizer-display-swatch-wrapper" data-size-selector="{$this->getVariable()}"></span>
<script>
$(function() {
    $('span[data-size-selector={$this->getVariable()}]').concreteSizeSelector({$strOptions});
});
</script>
EOT
        ;
    }

    /**
     * @return \Concrete\Core\StyleCustomizer\Style\Value\SizeValue
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Style::getValueFromRequest()
     */
    public function getValueFromRequest(ParameterBag $request)
    {
        $size = $request->get($this->getVariable());
        $sv = new SizeValue($this->getVariable());
        $sv->setSize(isset($size['size']) ? $size['size'] : null);
        $sv->setUnit(isset($size['unit']) ? $size['unit'] : null);

        return $sv;
    }

    /**
     * Extract a size value from a Less node.
     *
     * @param \Less_Tree_Dimension|mixed $value
     * @param string|null|false $variable The associated CSS variable
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\SizeValue|null
     */
    public static function parse($value, $variable = false)
    {
        $sv = null;
        if ($value instanceof Less_Tree_Dimension) {
            if (isset($value->unit->numerator[0])) {
                $unit = $value->unit->numerator[0];
            } else {
                $unit = 'px';
            }
            $sv = new SizeValue($variable);
            $sv->setSize($value->value);
            $sv->setUnit($unit);
        }

        return $sv;
    }

    /**
     * @return \Concrete\Core\StyleCustomizer\Style\Value\SizeValue[]
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Style::getValuesFromVariables()
     */
    public static function getValuesFromVariables($rules = [])
    {
        $values = [];
        foreach ($rules as $rule) {
            if (preg_match('/@(.+)\-size/i', isset($rule->name) ? $rule->name : '', $matches)) {
                $value = $rule->value->value[0]->value[0];
                $sv = static::parse($value, $matches[1]);
                if ($sv) {
                    $values[] = $sv;
                }
            }
        }

        return $values;
    }
}
