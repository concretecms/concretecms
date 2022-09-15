<?php
namespace Concrete\Core\Health\Report\Finding\Control;

use Concrete\Core\Health\Report\Finding\Control\Formatter\DropdownItemFormatter;
use Concrete\Core\Health\Report\Finding\Control\Formatter\FormatterInterface;

class DropdownItemControl extends ButtonControl
{

    public function getFormatter(): FormatterInterface
    {
        return new DropdownItemFormatter();
    }


}
