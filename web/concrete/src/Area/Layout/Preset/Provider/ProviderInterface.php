<?php
namespace Concrete\Core\Area\Layout\Preset\Provider;

use Concrete\Core\Page\Page;

interface ProviderInterface
{

    public function getPresets();
    public function getName();

}