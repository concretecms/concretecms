<?php
namespace Concrete\Core\StyleCustomizer\Style\Value;
class ImageValue extends Value {

    protected $imageUrl;

    public function setUrl($url)
    {
        $this->imageUrl = $url;
    }

    public function getUrl()
    {
        return $this->imageUrl;
    }

    public function toStyleString()
    {
        return '';
    }
}