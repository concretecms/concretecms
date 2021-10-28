<?php
namespace Concrete\Core\Summary\Data\Field;

use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Entity\File\File as FileEntity;
use League\Url\Url;
use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Concrete\Core\File\File;

/**
 * Represents a link. Useful for linking to pages on your site, because the contents are run through the
 * Concrete\Core\Editor\LinkAbstractor class, ensuring that if your site is migrated elsewhere the cached
 * contents of the data field will translate to the new environment.
 */
class LinkDataFieldData implements DataFieldDataInterface
{

    /**
     * @var int
     */
    protected $link;
    
    public function __construct(string $link = null)
    {
        $this->setData($link);
    }

    /**
     * @param string|Url
     */
    public function setData($link): void
    {
        $abstractor = new LinkAbstractor();
        $this->link = $abstractor->translateTo((string) $link);
    }
    
    public function __toString()
    {
        $abstractor = new LinkAbstractor();
        return $abstractor->translateFrom($this->link);
    }
    
    public function jsonSerialize()
    {
        return [
            'class' => self::class,
            'link' => (string) $this->link
        ];
    }
    
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        if (isset($data['link'])) {
            $this->setData($data['link']);
        }
    }
}
