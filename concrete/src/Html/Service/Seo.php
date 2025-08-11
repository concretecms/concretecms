<?php

namespace Concrete\Core\Html\Service;

class Seo
{
    protected $siteName = '';

    protected $titleSegments = [];

    protected $titleSegmentSeparator = ' :: ';

    protected $titleFormat = '%1$s :: %2$s';

    protected $hasCustomTitle = false;

    public function setSiteName($name)
    {
        $this->siteName = $name;
    }

    public function hasCustomTitle()
    {
        return $this->hasCustomTitle;
    }

    public function setCustomTitle($title)
    {
        $this->hasCustomTitle = true;
        $this->clearTitleSegments();
        $this->addTitleSegmentBefore($title);

        return $this;
    }

    public function getTitleSegments()
    {
        return $this->titleSegments;
    }

    public function addTitleSegment($segment)
    {
        array_push($this->titleSegments, $segment);

        return $this;
    }

    public function addTitleSegmentBefore($segment)
    {
        array_unshift($this->titleSegments, $segment);

        return $this;
    }

    public function clearTitleSegments()
    {
        $this->titleSegments = [];
    }

    public function setTitleFormat($format)
    {
        $this->titleFormat = $format;

        return $this;
    }

    public function setTitleSegmentSeparator($separator)
    {
        $this->titleSegmentSeparator = $separator;

        return $this;
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
