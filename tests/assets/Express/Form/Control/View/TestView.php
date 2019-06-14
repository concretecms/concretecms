<?php
namespace Concrete\Tests\Express\Form\Control\View;

use Concrete\Core\Express\Form\Control\View\View;
use Concrete\Core\Filesystem\TemplateLocator;

class TestView extends View
{
    public function getControlID()
    {
        return 'tests';
    }

    public function createTemplateLocator()
    {
        $locator = new TemplateLocator('tests');

        return $locator;
    }
}
