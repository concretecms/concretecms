<?php
namespace Concrete\Core\Summary\Data\Field;

use Concrete\Core\Entity\File\File as FileEntity;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Concrete\Core\File\File;

class ImageDataFieldData implements DataFieldDataInterface
{

    /**
     * @var int
     */
    protected $fID;
    
    public function __construct(FileEntity $f = null)
    {
        if ($f) {
            $this->fID = $f->getFileID();
        }
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

    public function getThumbnailURL(string $thumbnailType)
    {
        $file = File::getByID($this->fID);
        if ($file) {
            return $file->getThumbnailURL($thumbnailType);
        }
    }
    
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'class' => self::class,
            'fID' => (string) $this->fID
        ];
    }
    
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        if (isset($data['fID'])) {
            $this->setData($data['fID']);
        }
    }
}
