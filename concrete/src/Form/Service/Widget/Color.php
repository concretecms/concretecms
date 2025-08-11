<?php

namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Http\Request;
use Concrete\Core\View\View;

class Color
{
    /**
     * Creates form fields and JavaScript includes to add a color picker widget.
     * <code>
     *     $dh->output('background-color', '#f00');
     * </code>.
     *
     * @param string $inputName
     * @param string|null $value
     * @param array $options
     */
    public function output($inputName, $value = null, $options = [])
    {
        $r = Request::getInstance();
        if ($r->request->has($inputName)) {
            $value = h($r->request->get($inputName));
        }
        $strOptions = '';
        $defaults = [
            'value' => $value,
            'type' => 'color',
            'className' => 'ccm-widget-colorpicker',
            'showInitial' => true,
            'showInput' => true,
            'allowEmpty' => true,
            'cancelText' => t('Cancel'),
            'chooseText' => t('Choose'),
            'togglePaletteMoreText' => t('more'),
            'togglePaletteLessText' => t('less'),
            'noColorSelectedText' => t('No Color Selected'),
            'preferredFormat' => 'rgb',
            'showAlpha' => false,
            'clearText' => t('Clear Color Selection'),
            'appendTo' => '.ui-dialog',
        ];
        $strOptions = json_encode(array_merge($defaults, $options));

        $identifier = new \Concrete\Core\Utility\Service\Identifier();
        $identifier = $identifier->getString(32);

        echo <<<EOT
<input type="text" data-color-picker="{$identifier}" name="{$inputName}" value="{$value}" id="ccm-colorpicker-{$inputName}" />
<script>
$(function () {
    $('[data-color-picker={$identifier}]').spectrum({$strOptions});
});
</script>
EOT
        ;
    }
}
