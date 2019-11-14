<?php
namespace Concrete\Core\Summary\Category;

use Concrete\Core\Summary\Template\RenderableTemplateInterface;

interface CategoryMemberInterface
{

    /**
     * @return string
     */
    public function getSummaryCategoryHandle() : string;

    /**
     * @return RenderableTemplateInterface[]
     */
    public function getSummaryTemplates() : array;
    
}
