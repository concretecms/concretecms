<?php

namespace Concrete\TestHelpers\Config\Repository;

use Concrete\Core\Config\SaverInterface;

class LiaisonSaver implements SaverInterface
{
    public $saved = false;

    public function save($item, $value, $environment, $group, $namespace = null)
    {
        $this->saved = "{$namespace}::{$group}.{$item}";
    }
}
