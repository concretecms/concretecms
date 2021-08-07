<?php
namespace Concrete\Controller\PageType;

use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\PageTypeController;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;

class CoreThemeDocumentation extends PageTypeController
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
        $documentationPage = Page::getByPath('/dashboard/pages/themes');
        $permissions = new Checker($documentationPage);

        // Make sure we can view the stacks page
        if ($permissions->canViewPage()) {
            $parent = Page::getByID($this->getPageObject()->getCollectionParentID());
            $themeHandle = $parent->getCollectionHandle();
            $this->setTheme($themeHandle);
            return;
        }

        // If we can't view the stacks page, send a 404
        return $this->factory->notFound('');
    }

}
