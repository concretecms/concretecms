<?php
namespace Concrete\Core\Area\Layout\Preset;

use Concrete\Core\Page\Page;

interface ProviderInterface
{

    public function getPresets(Page $page);
    public function getName();

}