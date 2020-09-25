<?php
namespace Concrete\Core\Legacy\Controller;

use Controller;
use Environment;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\View\DialogView;

class ToolController extends Controller
{
    public function getTheme()
    {
        return false;
    }

    public function display($tool)
    {
        $env = Environment::get();
        $query = false;
        if (substr($tool, 0, 9) != 'required/') {
            $path = DIR_APPLICATION . '/' . DIRNAME_TOOLS . '/' . $tool . '.php';
        } else {
            $tool = substr($tool, 9);
            $path = DIR_BASE_CORE . '/' . DIRNAME_TOOLS . '/' . $tool . '.php';
        }
        if (is_file($path)) {
            $realpath = realpath($path);
            if (($realpath !== false) && \Core::make('helper/file')->isSamePath($path, $realpath)) {
                $query = $tool;
            }
        }

        if ($query) {
            $v = new DialogView($query);
            $v->setViewRootDirectoryName(DIRNAME_TOOLS);
            $this->setViewObject($v);
        } else {
            return $this->app->make(ResponseFactoryInterface::class)->notFound('');
        }
    }
}
