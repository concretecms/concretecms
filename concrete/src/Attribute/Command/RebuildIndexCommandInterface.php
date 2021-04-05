<?php

namespace Concrete\Core\Attribute\Command;

use Concrete\Core\Attribute\Category\CategoryInterface;

interface RebuildIndexCommandInterface
{

    public function getAttributeKeyCategory(): CategoryInterface;

    public function getIndexName();

}