<?php
namespace Concrete\Core\Health\Report\Test\Test;

use Concrete\Core\Health\Report\Test\PageTestInterface;

abstract class AbstractPageTest implements PageTestInterface
{

    protected $pageId;

    /**
     * @return int
     */
    public function getPageId(): int
    {
        return $this->pageId;
    }

    /**
     * @param int $pageId
     */
    public function setPageId(int $pageId): void
    {
        $this->pageId = $pageId;
    }


}
