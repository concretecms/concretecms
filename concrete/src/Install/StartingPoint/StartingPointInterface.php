<?php

namespace Concrete\Core\Install\StartingPoint;

use Concrete\Core\Package\StartingPointPackage;

interface StartingPointInterface extends \JsonSerializable
{

    public function getIdentifier(): string;

    public function getName(): string;


}
