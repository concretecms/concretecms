<?

namespace Concrete\Controller\SinglePage\Dashboard\System\Basics;
use Concrete\Core\Editor\RedactorEditor;
use \Concrete\Core\Page\Controller\DashboardPageController;

class Editor extends DashboardPageController
{

    public function view()
    {
        $manager = \Core::make("editor")->getPluginManager();
        $plugins = $manager->getAvailablePlugins();
        $this->set('plugins', $plugins);
        $this->set('manager', $manager);
    }


}
