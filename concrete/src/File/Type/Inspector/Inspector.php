<?php
namespace Concrete\Core\File\Type\Inspector;

use Concrete\Core\File\Version;

abstract class Inspector {

	abstract public function inspect(Version $fv);


}
