<?php
namespace Concrete\Core\Html\Service;

class Seo
{
    private $siteName = '';
    private $titleSegments = array();
    private $titleSegmentSeparator = ' :: ';
    private $titleFormat = '%1$s :: %2$s';
    
    public function setSiteName($name)
    {
        $this->siteName = $name;
    }
    
    public function addTitleSegment($segment)
    {
        array_unshift($this->titleSegments, $segment);
    }
    
    public function setTitleFormat($format)
    {
        $this->titleFormat = $format;
    }
    
    public function setTitleSegmentSeparator($separator)
    {
        $this->titleSegmentSeparator = $separator;
    }
    
    public function getTitle()
    {
        $segments = '';
        if (count($this->titleSegments) > 0) {
            $segments = implode($this->titleSegmentSeparator, $this->titleSegments);
        }
        return sprintf($this->titleFormat, $this->siteName, $segments);
    }
}
