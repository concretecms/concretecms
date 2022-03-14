<?php
namespace Concrete\Core\Summary\Data\Field;

use League\Url\Url;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Represents a link.
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
        $this->link = $link;
    }
    
    public function __toString()
    {
        return (string) $this->link;
    }
    
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'class' => static::class,
            'link' => (string) $this->link,
        ];
    }
    
    public function denormalize(DenormalizerInterface $denormalizer, $data, $format = null, array $context = [])
    {
        if (isset($data['link'])) {
            $this->setData($data['link']);
        }
    }
}
