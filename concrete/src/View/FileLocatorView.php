<?php
namespace Concrete\Core\View;

use Concrete\Core\Filesystem\FileLocator;
use Concrete\Core\Filesystem\FileLocatorInterface;
use Concrete\Core\Filesystem\LocatableFileInterface;

class FileLocatorView extends AbstractView
{

    /**
     * @var $locator LocatableFileInterface
     */
    protected $locator;

    public function constructView($locator)
    {
        $this->locator = $locator;
    }

    public function start($state)
    {
        if ($this->controller) {
            $this->controller->runTask('view', array());
        }
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
        $r = $this->locator->getFileLocatorRecord();
        if (!$r->exists()) {
            throw new \RuntimeException(t('File %s does not exist', $r->file));
        }
        $this->setViewTemplate($r->getFile());
    }

    public function finishRender($contents)
    {
        echo $contents;
    }
}
