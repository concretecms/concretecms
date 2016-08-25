<?php
namespace Concrete\Core\Express\Form\Control\Template;

use Concrete\Core\Express\Form\Context\ContextInterface;

class Template implements TemplateInterface
{

    protected $context;
    protected $templateSegments = [];

    public function __construct(ContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * @return array
     */
    public function getTemplateSegments()
    {
        return $this->templateSegments;
    }

    /**
     * @param array $templateSegments
     */
    public function addTemplateSegment($templateSegment)
    {
        $this->templateSegments[] = $templateSegment;
    }

    public function getFile()
    {
        $segments = [DIRNAME_ELEMENTS, DIRNAME_EXPRESS, DIRNAME_EXPRESS_FORM_CONTROLS];
        $segments[] = $this->context->getContextHandle();
        $segments = array_merge($segments, $this->templateSegments);
        $path = implode('/', $segments);
        return $this->context->getApplication()->make('environment')->getPath($path . '.php');
    }

}