<?php

namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Page\Page;

class AddHomePageRoutineHandler
{

    public function __invoke()
    {
        Page::addHomePage();
    }


}
