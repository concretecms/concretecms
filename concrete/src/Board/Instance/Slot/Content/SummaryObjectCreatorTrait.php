<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Template\RendererFilterer;
use Concrete\Core\Summary\SummaryObject as BaseSummaryObject;
trait SummaryObjectCreatorTrait
{
    
    public function createSummaryContentObjects(CategoryMemberInterface $mixed) : array
    {
        $objects = [];
        if ($mixed->hasCustomSummaryTemplates()) {
            $templates = $mixed->getCustomSelectedSummaryTemplates();
        } else {
            $templates = $mixed->getSummaryTemplates();
        }
        foreach($templates as $template) {
            $objects[] = new SummaryObject(
                new BaseSummaryObject(
                    $mixed->getSummaryCategoryHandle(), 
                    $mixed->getSummaryIdentifier(),
                    $template->getTemplate(),
                    $template->getData()
                )
            );
        }
        return $objects;        
    }

    

}
