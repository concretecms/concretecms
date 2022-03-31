<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

use Concrete\Core\File\File;

class ImageVariable implements VariableInterface
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string|null
     */
    protected $fID;

    /**
     * ImageVariable constructor.
     * @param string $name
     */
    public function __construct(string $name, string $url = null, $fID = null)
    {
        $this->name = $name;
        $this->url = $url;
        $this->fID = $fID;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string|null
     */
    public function getFileID(): ?string
    {
        return $this->fID;
    }

    /**
     * @param string|null $fID
     */
    public function setFileID($fID): void
    {
        $this->fID = $fID;
    }

    public function getComputedUrl()
    {
        $url = null;
        if ($this->fID) {
            $file = File::getByID($this->fID);
            if ($file) {
                $url = $file->getURL();
            }
        }
        if (!$url) {
            $url = $this->getUrl();
        }
        return $url;
    }

    public function getValue()
    {
        return sprintf("url('%s')", $this->getComputedUrl());
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'type' => 'image',
            'name' => $this->getName(),
            'url' => $this->getUrl(),
            'fID' => $this->getFileID()
        ];
    }


}
