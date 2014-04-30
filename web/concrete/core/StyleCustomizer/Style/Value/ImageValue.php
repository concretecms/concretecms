<?php
namespace Concrete\Core\StyleCustomizer\Style\Value;
class ImageValue extends Value {

    protected $imagePath;

    public function setPath($path)
    {
        $this->imagePath = $path;
    }

    public function getPath()
    {
        return $this->imagePath;
    }

}