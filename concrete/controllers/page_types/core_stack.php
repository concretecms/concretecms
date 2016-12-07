<?php
namespace Concrete\Controller\PageType;

use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\PageTypeController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker as Permissions;

class CoreStack extends PageTypeController
{

    /**
     * @var \Concrete\Core\Http\ResponseFactory
     */
    private $factory;

    public function __construct(\Concrete\Core\Page\Page $c, ResponseFactory $factory)
    {
        parent::__construct($c);
        $this->factory = $factory;
    }

    public function on_start()
    {
        $stacksPage = Page::getByPath('/dashboard/blocks/stacks');
        $stacksPerms = new Permissions($stacksPage);

        // Make sure we can view the stacks page
        if ($stacksPerms->canViewPage()) {
            $currentPage = $this->c;
            $currentPagePerms = new Permissions($currentPage);
            $viewTask = $this->request->get('vtask');

            // If the current user can't view this pages versions, or if vtask is not one of the available tasks
            if (!$currentPagePerms->canViewPageVersions() || !in_array($viewTask, ['view_versions', 'compare'])) {
                $url = $stacksPage->getPageController()->action('view_details', $currentPage->getCollectionID());

                // Redirect to the stacks page
                return $this->factory->redirect($url);
            } else {
                // Otherwise set the current theme and render normally
                $this->theme = 'dashboard';
            }
        }

        // If we can't view the stacks page, send a 404
        return $this->factory->notFound('');
    }

}
