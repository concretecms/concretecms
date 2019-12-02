<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\Template\RendererFilterer;

trait SummaryObjectCreatorTrait
{
    
    public function createSummaryContentObject(CategoryMemberInterface $mixed) : ObjectInterface
    {
        /**
         * @var $rendererFilterer RendererFilterer
         */
        $rendererFilterer = $this->app->make(RendererFilterer::class);
        $template = $rendererFilterer->getRandomTemplate($mixed);
        if ($template) {
            return new SummaryObject($template);
        }
        
    }

    

}
