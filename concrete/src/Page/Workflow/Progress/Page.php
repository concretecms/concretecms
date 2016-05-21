<?php
namespace Concrete\Core\Page\Workflow\Progress;

use Concrete\Core\Workflow\Progress\PageProgress;

class Page
{
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
