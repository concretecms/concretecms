<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\File\File;
use Concrete\Core\StyleCustomizer\Normalizer\ImageVariable;
use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Normalizer\VariableInterface;
use Concrete\Core\StyleCustomizer\Style\Value\ImageValue;
use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;
use Concrete\Core\Utility\Service\Validation\Numbers;

class ImageStyle extends Style
{

    public function createValueFromVariableCollection(NormalizedVariableCollection $collection): ?ValueInterface
    {
        $variable = $collection->getVariable($this->getVariable());
        $value = new ImageValue();
        if ($variable instanceof ImageVariable) {
            /**
             * @var $variable ImageVariable
             */
            $numbers = new Numbers();
            if ($numbers->integer($variable->getFileID())) {
                // This is  file ID.
                $value->setImageFileID($variable->getFileID());
            } else {
                // is it a URL?
                if ($variable->getUrl()) {
                    $value->setImageURL($variable->getUrl());
                }
            }
            return $value;
        }
        return $value;
    }

    public function createValueFromRequestDataCollection(array $styles): ?ValueInterface
    {
        foreach ($styles as $style) {
            if (isset($style['variable']) && $style['variable'] == $this->getVariable()) {
                $value = new ImageValue();
                if (!empty($style['value']['imageURL'])) {
                    $value->setImageURL($style['value']['imageURL']);
                }
                if (!empty($style['value']['imageFileID'])) {
                    $value->setImageFileID($style['value']['imageFileID']);
                }
                return $value;
            }
        }
        return null;
    }

    /**
     * @param ImageValue $value
     * @return VariableInterface|null
     */
    public function createVariableFromValue(ValueInterface $value): ?VariableInterface
    {
        $url = null;
        $fID = null;
        if ($value->getImageFileID()) {
            // An editor/admin has set the background image using the customizer, so it takes precedence
            $file = File::getByID($value->getImageFileID());
            if ($file) {
                $fID = $file->getFileID();
            }
        }
        if (!$fID) {
            if ($value->getImageURL()) {
                $url = $value->getImageURL();
            }
        }
        $variable = new ImageVariable($this->getVariable(), $url);
        if ($fID) {
            $variable->setFileID($fID);
        }
        return $variable;
    }

}
