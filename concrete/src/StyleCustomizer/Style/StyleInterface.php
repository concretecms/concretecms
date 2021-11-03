<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\StyleCustomizer\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Normalizer\VariableInterface;
use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;

interface StyleInterface extends \JsonSerializable
{

    public function getName();

    public function getVariable();

    public function getVariableToInspect();

    public function createValueFromVariableCollection(NormalizedVariableCollection $collection) :?ValueInterface;

    public function createValueFromRequestDataCollection(array $styles) :?ValueInterface;

    public function createVariableFromValue(ValueInterface $value) :?VariableInterface;

}
