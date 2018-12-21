<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

class ImageValue extends Value
{
    /**
     * The URL of the image.
     *
     * @var string
     */
    protected $imageUrl = '';

    /**
     * The ID of the associated File instance.
     *
     * @var int|null
     */
    protected $fID;

    /**
     * Set the URL of the image.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->imageUrl = (string) $url;

        return $this;
    }

    /**
     * Get the URL of the image.
     *
     * @return string
     */
    public function getUrl()
    {
        return (string) $this->imageUrl;
    }

    /**
     * Set the ID of the associated File instance.
     *
     * @param int|null $fID
     *
     * @return $this
     */
    public function setFileID($fID)
    {
        $this->fID = $fID ? (int) $fID : null;
    }

    /**
     * Get the ID of the associated File instance.
     *
     * @return int|null
     */
    public function getFileID()
    {
        return $this->fID ? (int) $this->fID : null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Value\Value::toStyleString()
     */
    public function toStyleString()
    {
        return 'background-image: url(' . $this->getUrl() . ')';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\StyleCustomizer\Style\Value\Value::toLessVariablesArray()
     */
    public function toLessVariablesArray()
    {
        return [$this->getVariable() . '-image' => "'" . $this->getUrl() . "'"];
    }
}
