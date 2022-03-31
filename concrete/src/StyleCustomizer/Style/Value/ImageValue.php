<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

class ImageValue extends Value
{
    /**
     * @var string
     */
    protected $imageURL;

    /**
     * @var integer
     */
    protected $imageFileID;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getImageURL(): ?string
    {
        return $this->imageURL;
    }

    /**
     * @param string $imageURL
     */
    public function setImageURL(string $imageURL): void
    {
        $this->imageURL = $imageURL;
    }

    /**
     * @return int
     */
    public function getImageFileID(): ?int
    {
        return $this->imageFileID;
    }

    /**
     * @param int $imageFileID
     */
    public function setImageFileID(int $imageFileID): void
    {
        $this->imageFileID = $imageFileID;
    }


    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'imageURL' => $this->getImageURL(),
            'imageFileID' => $this->getImageFileID(),
        ];
    }




}
