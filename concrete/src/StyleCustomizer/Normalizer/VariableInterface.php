<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

interface VariableInterface extends \JsonSerializable
{


    public function getName(): string;

    public function getValue();


}
