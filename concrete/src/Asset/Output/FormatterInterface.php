<?php

namespace Concrete\Core\Asset\Output;

use Concrete\Core\Asset\Asset;

/**
 * @since 8.0.0
 */
interface FormatterInterface
{

    public function output(Asset $asset);

}
