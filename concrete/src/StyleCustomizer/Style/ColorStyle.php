<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\StyleCustomizer\Style\Value\ColorValue;
use Concrete\Core\Support\Less\TreeCallColor;
use Less_Tree_Call;
use Less_Tree_Color;
use Primal\Color\Parser;
use Symfony\Component\HttpFoundation\ParameterBag;

class ColorStyle extends Style
{
    /**
     * @param \Concrete\Core\StyleCustomizer\Style\Value\ColorValue|null|false $value
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Style::render()
     */
    public function render($value = false)
    {
        $inputName = $this->getVariable();
        $r = Request::getInstance();
        if ($r->request->has($inputName)) {
            $color = h($r->request->get($inputName));
        } elseif ($value) {
            $color = $value->toStyleString();
        } else {
            $color = '';
        }
        
        $json = [
            'color' => json_encode($color),
            'cancel' => json_encode(t('Cancel')),
            'choose' => json_encode(t('Choose')),
            'clear' => json_encode(t('Clear Color Selection')),
        ];

        echo <<<EOT
<input type="text" name="{$inputName}[color]" value="{$color}" id="ccm-colorpicker-{$inputName}" />
<script>
$(function() {
    $('#ccm-colorpicker-{$inputName}').concreteStyleCustomizerColorPicker({
        initialColor: {$json['color']},
        i18n: {
            cancel: {$json['cancel']},
            choose: {$json['choose']},
            clear: {$json['clear']}
        }
    });
});
</script>



<script>
$(function() {
    $('#ccm-colorpicker-{$inputName}').spectrum({
        showInput: true,
        showInitial: true,
        preferredFormat: 'rgb',
        allowEmpty: true,
        className: 'ccm-widget-colorpicker',
        showAlpha: true,
        value: {$json['color']},
        cancelText: {$json['cancel']},
        chooseText: {$json['choose']},
        clearText: {$json['clear']},
        change: function() {
            ConcreteEvent.publish('StyleCustomizerControlUpdate');
        }
    });
});
</script>
EOT
        ;
    }

    /**
     * Extract a color value from a Less node.
     *
     * @param \Less_Tree_Color|\Less_Tree_Call|mixed $value
     * @param string|null|false $variable The associated CSS variable
     *
     * @return \Concrete\Core\StyleCustomizer\Style\Value\ColorValue|null
     */
    public static function parse($value, $variable = false)
    {
        $cv = null;
        if ($value instanceof Less_Tree_Color) {
            if ($value->isTransparentKeyword) {
                return false;
            }
            $cv = new ColorValue($variable);
            $cv->setRed($value->rgb[0]);
            $cv->setGreen($value->rgb[1]);
            $cv->setBlue($value->rgb[2]);
        } elseif ($value instanceof Less_Tree_Call) {
            // Extract the arguments from the value
            $color = TreeCallColor::fromTreeCall($value);
            $args = $color->getArguments();

            // might be rgb() or rgba()
            $cv = new ColorValue($variable);
            $cv->setRed($args[0]->value[0]->value);
            $cv->setGreen($args[1]->value[0]->value);
            $cv->setBlue($args[2]->value[0]->value);
            if ($color->getName() == 'rgba') {
                $cv->setAlpha($args[3]->value[0]->value);
            }
        }

        return $cv;
    }

    /**
     * @return \Concrete\Core\StyleCustomizer\Style\Value\ColorValue|null
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Style::getValueFromRequest()
     */
    public function getValueFromRequest(ParameterBag $request)
    {
        $color = $request->get($this->getVariable());
        if (!$color['color']) { // transparent
            return null;
        }
        $cv = new Parser($color['color']);
        $result = $cv->getResult();
        $alpha = false;
        if (is_numeric($result->alpha) && $result->alpha >= 0 && $result->alpha < 1) {
            $alpha = $result->alpha;
        }
        $cv = new ColorValue($this->getVariable());
        $cv
            ->setRed($result->red)
            ->setGreen($result->green)
            ->setBlue($result->blue)
            ->setAlpha($alpha)
        ;

        return $cv;
    }

    /**
     * @return \Concrete\Core\StyleCustomizer\Style\Value\ColorValue[]
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Style::getValuesFromVariables()
     */
    public static function getValuesFromVariables($rules = [])
    {
        $values = [];
        foreach ($rules as $rule) {
            if (preg_match('/@(.+)\-color/i', isset($rule->name) ? $rule->name : '', $matches)) {
                $value = $rule->value->value[0]->value[0];
                $cv = static::parse($value, $matches[1]);
                if ($cv) {
                    $values[] = $cv;
                }
            }
        }

        return $values;
    }
}
