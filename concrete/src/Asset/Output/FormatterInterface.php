<?php

namespace Concrete\Core\Asset\Output;

use Concrete\Core\Asset\Asset;

interface FormatterInterface
{

    public function output(Asset $asset);

}
