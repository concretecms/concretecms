<?php

namespace Concrete\Core\Form\Service\Widget;

use Concrete\Core\Http\ResponseAssetGroup;

class Typography
{
    /**
     * Creates form fields and JavaScript includes to add a font picker widget.
     *
     * @param string $inputName
     * @param array $value
     * @param array $options
     *
     * @example <code>$dh->output('background-color', '#f00');</code>
     */
    public function output($inputName, $value = [], $options = [])
    {
        $options['inputName'] = $inputName;
        $options = array_merge($options, $value);
        $strOptions = json_encode($options);

        echo <<<EOT
<span class="ccm-style-customizer-display-swatch-wrapper" data-font-selector="{$inputName}"></span>
<script>
$(function () {
    $('span[data-font-selector={$inputName}]').concreteTypographySelector({$strOptions});
})
</script>
EOT
        ;
    }
}
