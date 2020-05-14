<?php
namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Foundation\Queue\Response\QueueProgressResponse;
use Concrete\Core\Summary\Category\Driver\DriverInterface;
use Concrete\Core\Summary\Category\Driver\Manager;
use Concrete\Core\Summary\Template\Renderer;

class SummaryTemplate extends Controller
{

    protected $viewPath = '/summary_template/render';

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

    }
}
