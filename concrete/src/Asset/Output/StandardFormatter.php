<?php
namespace Concrete\Core\Asset\Output;

use Concrete\Core\Asset\Asset;

class StandardFormatter implements FormatterInterface
{

    public function output(Asset $asset)
    {
        return $asset;
    }
}