<?php

namespace Concrete\Core\Attribute\Command;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;

interface RebuildIndexCommandInterface extends NormalizableInterface, DenormalizableInterface
{

    public function getAttributeKeyCategory(): CategoryInterface;

    public function getIndexName();

}