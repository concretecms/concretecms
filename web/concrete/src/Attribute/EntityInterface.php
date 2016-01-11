<?php
namespace Concrete\Core\Attribute;

interface EntityInterface
{
    const ASET_ALLOW_NONE = 0;
    const ASET_ALLOW_SINGLE = 1;
    const ASET_ALLOW_MULTIPLE = 2;

    public function getAttributeKeyCategory();
    public function allowAttributeSets();
    public function getAttributeSets();
}
