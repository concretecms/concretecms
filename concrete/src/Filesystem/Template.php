<?php
namespace Concrete\Core\Filesystem;

class Template
{

    protected $templateHandle;
    protected $pkgHandle;

    /**
     * Template constructor.
     * @param $templateHandle
     * @param $pkgHandle
     */
    public function __construct($templateHandle, $pkgHandle = null)
    {
        $this->templateHandle = $templateHandle;
        $this->pkgHandle = $pkgHandle;
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

    /**
     * @return mixed
     */
    public function getPackageHandle()
    {
        return $this->pkgHandle;
    }

    /**
     * @param mixed $pkgHandle
     */
    public function setPackageHandle($pkgHandle)
    {
        $this->pkgHandle = $pkgHandle;
    }





}
