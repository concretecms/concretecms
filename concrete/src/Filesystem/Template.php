<?php
namespace Concrete\Core\Filesystem;

class Template
{

    protected $templateHandle;

    /**
     * Template constructor.
     * @param $templateHandle
     * @param $pkgHandle
     */
    public function __construct($templateHandle)
    {
        $this->templateHandle = $templateHandle;
    }

    /**
     * @return mixed
     */
    public function getTemplateHandle()
    {
        return $this->templateHandle;
    }

    /**
     * @param mixed $templateHandle
     */
    public function setTemplateHandle($templateHandle)
    {
        $this->templateHandle = $templateHandle;
    }



}
