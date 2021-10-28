<?php
namespace Concrete\Core\Summary\Category\Driver;

use Concrete\Core\Entity\Page\Summary\PageTemplate;
use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Template\RenderableTemplateInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class PageDriver extends AbstractDriver
{

    public function getCategoryMemberFromIdentifier($identifier): ?CategoryMemberInterface
    {
        return Page::getByID($identifier, 'ACTIVE');
    }

    public function getMemberSummaryTemplate($templateID): ?RenderableTemplateInterface
    {
        return $this->entityManager->find(PageTemplate::class, $templateID);
    }

}
