<?php
namespace Concrete\Core\Express\Form\Control\Template;

use Concrete\Core\Express\Form\Context\ContextInterface;

class Template implements TemplateInterface
{

    protected $template;

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function getTemplateHandle()
    {
        return $this->template;
    }

}