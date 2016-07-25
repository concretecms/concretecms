<?php
namespace Concrete\Core\Page\Type\Composer\Control;

use Concrete\Core\Foundation\Object;

class CustomTemplate extends Object
{
    protected $ptComposerControlCustomTemplateFilename;
    protected $ptComposerControlCustomTemplateName;

    public function __construct($ptComposerControlCustomTemplateFilename, $ptComposerControlCustomTemplateName)
    {
        $this->ptComposerControlCustomTemplateFilename = $ptComposerControlCustomTemplateFilename;
        $this->ptComposerControlCustomTemplateName = $ptComposerControlCustomTemplateName;
    }

    public function getPageTypeComposerControlCustomTemplateFilename()
    {
        return $this->ptComposerControlCustomTemplateFilename;
    }
    public function getPageTypeComposerControlCustomTemplateName()
    {
        return $this->ptComposerControlCustomTemplateName;
    }
}
