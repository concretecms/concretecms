<?php

namespace Concrete\Core\Summary\Category\Driver;

use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Template\RenderableTemplateInterface;

defined('C5_EXECUTE') or die("Access Denied.");

interface DriverInterface
{

    public function getCategoryMemberFromIdentifier($identifier): ?CategoryMemberInterface;

    public function getMemberSummaryTemplate($templateID): ?RenderableTemplateInterface;

}
