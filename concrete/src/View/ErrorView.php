<?php
namespace Concrete\Core\View;

use Environment;

class ErrorView extends View
{
    protected $error;

    protected function constructView($error)
    {
        $this->error = $error;
    }
    public function action($action)
    {
        throw new \Exception(t('Action is not available here.'));
    }

    protected function runControllerTask()
    {
    }
    public function setupRender()
    {
        $this->setViewTheme(VIEW_CORE_THEME);
        $this->loadViewThemeObject();

        $env = Environment::get();
        $r = $env->getPath(DIRNAME_THEMES . '/' . DIRNAME_THEMES_CORE . '/' . FILENAME_THEMES_ERROR . '.php');
        $this->setViewTemplate($r);
    }

    public function getScopeItems()
    {
        $items = parent::getScopeItems();
        $items['innerContent'] = $this->error->content;
        $items['titleContent'] = $this->error->title;

        return $items;
    }
}
