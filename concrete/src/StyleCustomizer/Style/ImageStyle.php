<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\File\File;
use Concrete\Core\Http\ResponseAssetGroup;
use Concrete\Core\Permission\Checker;
use Concrete\Core\StyleCustomizer\Style\Value\ImageValue;
use Concrete\Core\Support\Facade\Application;
use Less_Environment;
use Symfony\Component\HttpFoundation\ParameterBag;

class ImageStyle extends Style
{
    /**
     * @param \Concrete\Core\StyleCustomizer\Style\Value\ImageValue|null|false $value
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Style::render()
     */
    public function render($value = false)
    {
        $options = [
            'inputName' => $this->getVariable(),
        ];
        if ($value) {
            $options['value'] = $value->getUrl();
        }
        $strOptions = json_encode($options);

        echo <<<EOT
<span class="ccm-style-customizer-display-swatch-wrapper" data-image-selector="{$this->getVariable()}"></span>
<script>
$(function() {
    $('span[data-image-selector={$this->getVariable()}]').concreteStyleCustomizerImageSelector({$strOptions});
});
</script>
EOT
        ;
    }

    /**
     * @return \Concrete\Core\StyleCustomizer\Style\Value\ImageValue|null
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Style::getValueFromRequest()
     */
    public function getValueFromRequest(ParameterBag $request)
    {
        $iv = null;
        $image = $request->get($this->getVariable());
        $image = isset($image['image']) ? $image['image'] : null;
        if ($image) {
            $app = Application::getFacadeApplication();
            $nvh = $app->make('helper/validation/numbers');
            $iv = new ImageValue($this->getVariable());
            if ($nvh->integer($image)) {
                // it's a file ID.
                $f = File::getByID($image);
                if ($f) {
                    $fp = new Checker($f);
                    if ($fp->canViewFile()) {
                        $iv->setFileID($image);
                        $iv->setUrl($f->getRelativePath());
                    }
                }
            } else {
                $iv->setUrl($image);
            }
        }

        return $iv;
    }

    /**
     * @return \Concrete\Core\StyleCustomizer\Style\Value\ImageValue[]
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Style::getValuesFromVariables()
     */
    public static function getValuesFromVariables($rules = [])
    {
        $values = [];
        foreach ($rules as $rule) {
            if (preg_match('/@(.+)\-image/i', isset($rule->name) ? $rule->name : '', $matches)) {
                $entryURI = $rule->value->value[0]->value[0]->currentFileInfo['entryUri'];
                $value = $rule->value->value[0]->value[0]->value;
                if ($entryURI) {
                    $value = Less_Environment::normalizePath($entryURI . $value);
                }
                $iv = new ImageValue($matches[1]);
                $iv->setUrl($value);
                $values[] = $iv;
            }
        }

        return $values;
    }
}
