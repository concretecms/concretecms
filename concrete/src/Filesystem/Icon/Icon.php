<?php

namespace Concrete\Core\Filesystem\Icon;

/**
 * Class Icon
 * 
 * Responsible for representing an icon in different contexts.
 */
class Icon implements IconInterface
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
