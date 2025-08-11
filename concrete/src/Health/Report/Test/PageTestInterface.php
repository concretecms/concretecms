<?php
namespace Concrete\Core\Health\Report\Test;

use Concrete\Core\Health\Report\Runner;

interface PageTestInterface extends TestInterface
{

    public function getPageId(): int;

    public function setPageId(int $pageId);

}
