<?php

namespace Concrete\Core\StyleCustomizer\Style;

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\StyleCustomizer\Parser\Normalizer\NormalizedVariableCollection;
use Concrete\Core\StyleCustomizer\Style\Value\ValueInterface;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\HttpFoundation\ParameterBag;

interface StyleInterface extends \JsonSerializable
{

    public function getName();

    public function getVariable();

    public function createValueFromVariableCollection(NormalizedVariableCollection $collection) :?ValueInterface;

    public function createValueFromRequestDataCollection(array $styles) :?ValueInterface;

}
