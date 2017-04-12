<?php
namespace Concrete\Core\View;

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Support\Facade\Facade;
use Environment;
use Illuminate\Filesystem\Filesystem;

class ErrorView extends View
{
    protected $error;

    protected function constructView($error = false)
    {
        $this->error = $error;
    }
    public function action($action)
    {
        throw new \Exception(t('Action is not available here.'));
    }

    protected function onBeforeGetContents()
    {
        return false; // we don't want to run any internal events.
    }

    protected function runControllerTask()
    {
    }
    public function setupRender()
    {
        $this->setViewTheme(VIEW_CORE_THEME);
        $this->loadViewThemeObject();

        $locator = new FileLocator(new Filesystem(), Facade::getFacadeApplication());
        $r = $locator->getRecord(DIRNAME_THEMES . '/' . DIRNAME_THEMES_CORE . '/' . FILENAME_THEMES_ERROR . '.php');
        $this->setViewTemplate($r->getFile());
    }

    public function getScopeItems()
    {
        $items = parent::getScopeItems();
        $items['innerContent'] = $this->error ? $this->error->content : '';
        $items['titleContent'] = $this->error ? $this->error->title : '';

        return $items;
    }
}
