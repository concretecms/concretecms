<?php
namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Summary\Category\Driver\DriverInterface;
use Concrete\Core\Summary\Category\Driver\Manager;
use Concrete\Core\Summary\Template\Renderer;
use Concrete\Core\View\View;

class SummaryTemplate extends Controller
{

    protected $viewPath = '/summary_template/render';

    public function __construct()
    {
        parent::__construct();

        $this->view = new View($this->viewPath);
        $this->view->setViewTemplate('block_preview.php');
    }

    public function render($categoryHandle, $memberIdentifier, $templateID)
    {
        $manager = $this->app->make(Manager::class);
        $category = $manager->driver($categoryHandle);
        /**
         * @var $category DriverInterface
         */
        $template = $category->getMemberSummaryTemplate($templateID);
        $object = $category->getCategoryMemberFromIdentifier($memberIdentifier);
        $renderer = $this->app->make(Renderer::class);

        $this->set('template', $template);
        $this->set('renderer', $renderer);
        $this->set('object', $object);

        $theme = Theme::getSiteTheme();
        $this->setTheme($theme);
    }
}
