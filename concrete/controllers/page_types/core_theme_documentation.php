<?php
namespace Concrete\Controller\PageType;

use Concrete\Core\Html\Service\Navigation;
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
        // Make sure we can view the page
        if (!$permissions->canViewPage()) {
            return $this->factory->notFound('');
        }
    }

}
