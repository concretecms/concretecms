<?php
namespace Concrete\Core\Install\StartingPoint\Installer\Routine\Base;

use Concrete\Core\Install\StartingPoint\Installer\Routine\AbstractRoutine;

class InstallSiteRoutine extends AbstractRoutine
{

    public function getText(): string
    {
        return t('Installing site.');
    }


}
