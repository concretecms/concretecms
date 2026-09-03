<?php
namespace Concrete\Core\Page\Workflow\Progress;

use Concrete\Core\Workflow\Progress\PageProgress;

class Page
{
    /**
     * @var \Concrete\Core\Page\Page
     */
    protected $page;

    /**
     * @var \Concrete\Core\Workflow\Progress\PageProgress
     */
    protected $wp;

    public function __construct(\Concrete\Core\Page\Page $p, PageProgress $wp)
    {
        $this->page = $p;
        $this->wp = $wp;
    }

    public function getPageObject()
    {
        return $this->page;
    }
    public function getWorkflowProgressObject()
    {
        return $this->wp;
    }
}
