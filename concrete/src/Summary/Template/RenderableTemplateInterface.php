<?php
namespace Concrete\Core\Summary\Template;

use Concrete\Core\Entity\Summary\Template;
use Concrete\Core\Summary\Data\Collection;

interface RenderableTemplateInterface extends \JsonSerializable
{
    
    public function getData() : Collection;
    
    public function getTemplate() : Template;
    
}
