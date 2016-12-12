<?php
namespace Concrete\Core\StyleCustomizer\Style\Value;
class ImageValue extends Value {

    protected $imageUrl;
    protected $fID;

    public function setUrl($url)
    {
        $this->imageUrl = $url;
    }

    public function getUrl()
    {
        return $this->imageUrl;
    }

    public function setFileID($fID)
    {
        $this->fID = $fID;
    }

    public function getFileID()
    {
        return $this->fID;
    }

    public function toStyleString()
    {
        return 'background-image: url(' . $this->getUrl() . ')';
    }

    public function toLessVariablesArray()
    {
        return array($this->getVariable() . '-image' => '\'' . $this->getUrl() . '\'');
    }

}