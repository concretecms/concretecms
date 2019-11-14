<?php
namespace Concrete\Core\Summary\Data\Field;

use Concrete\Core\Entity\File\File as FileEntity;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Concrete\Core\File\File;

class ImageDataFieldData implements DataFieldDataInterface, DenormalizableInterface
{

    /**
     * @var int
     */
    protected $fID;
    
    public function __construct(FileEntity $f = null)
    {
        $this->fID = $f->getFileID();
    }

    /**
     * @param int
     */
    public function setData($fID): void
    {
        $this->fID = $fID;
    }
    
    public function __toString()
    {
        $file = File::getByID($this->fID);
        if ($file) {
            return $file->getURL();
        } else {
            return '';
        }
    }
    
    public function jsonSerialize()
    {
        return [
            'class' => self::class,
            'fID' => (string) $this->fID
        ];
    }
    
    public function denormalize(DenormalizerInterface $denormalizer, $fID, $format = null, array $context = [])
    {
        $this->setData($fID);
    }
}
