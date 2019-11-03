<?php

namespace Concrete\Core\Page\Container;

/**
 * Class Icon
 * 
 * Responsible for representing a container icon.
 */
class Icon
{

    /**
     * @var string 
     */
    protected $filename;

    /**
     * @var string 
     */
    protected $url;
    
    public function __construct(string $filename, string $url)
    {
        $this->filename = $filename;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    
    
}
