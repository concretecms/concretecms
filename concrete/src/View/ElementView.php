<?php
namespace Concrete\Core\View;

use Environment;

class ElementView extends AbstractView
{
    protected $pkgHandle;
    protected $element;

    public function constructView($mixed)
    {
        return false;
    }

    public function __construct($element, $pkgHandle = null)
    {
        $this->pkgHandle = $pkgHandle;
        $this->element = $element;
    }

    public function setPackageHandle($pkgHandle)
    {
        $this->pkgHandle = $pkgHandle;
    }

    /**
     * Begin the render.
     */
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
        $env = Environment::get();
        $r = $env->getRecord(DIRNAME_ELEMENTS . '/' . $this->element . '.php', $this->pkgHandle);
        if (!$r->exists()) {
            throw new \RuntimeException(t('Element %s does not exist', $this->element));
        }
        $this->setViewTemplate($r->file);
    }

    public function finishRender($contents)
    {
        echo $contents;
    }
}
