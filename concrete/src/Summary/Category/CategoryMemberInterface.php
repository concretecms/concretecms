<?php
namespace Concrete\Core\Summary\Category;

use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Summary\Template\RenderableTemplateInterface;

interface CategoryMemberInterface
{

    public function getSummaryIdentifier();
    
    /**
     * @return string
     */
    public function getSummaryCategoryHandle() : string;

    /**
     * @return RenderableTemplateInterface[]
     */
    public function getSummaryTemplates() : array;

    /**
     * @return bool
     */
    public function hasCustomSummaryTemplates() : bool;

    /**
     * @return Template[]
     */
    public function getCustomSelectedSummaryTemplates() : array;
    
}
