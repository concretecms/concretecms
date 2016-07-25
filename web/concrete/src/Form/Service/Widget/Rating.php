<?php
namespace Concrete\Core\Form\Service\Widget;

use Loader;

class Rating
{
    public function rating($prefix, $value = null, $includeJS = true)
    {
        $rt = Loader::helper('rating');

        return $rt->output($prefix, $value, true, $includeJS);
    }
}
