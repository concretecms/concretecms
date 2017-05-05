<?php
namespace Concrete\Core\View;

/**
 * Includes a file with all of the functionality of views and controller. Simply takes a path to a file.
 */
class BasicFileView extends AbstractView
{
    protected $file;

    public function constructView($file)
    {
        $this->file = $file;
    }

    public function start($state)
    {
        $this->controller->runTask('view', array());
    }

    protected function onBeforeGetContents()
    {
    }

    public function action($action)
    {
        $a = func_get_args();
        $c = \Page::getCurrentPage();
        array_unshift($a, $c);
        return call_user_func_array(array('\URL', 'to'), $a);
    }

    public function setupRender()
    {
        $this->setViewTemplate($this->file);
    }

    public function finishRender($contents)
    {
        echo $contents;
    }
}
