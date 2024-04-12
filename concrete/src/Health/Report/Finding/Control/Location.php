<?php
namespace Concrete\Core\Health\Report\Finding\Control;

use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Location implements LocationInterface
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $url;

    public function __construct(?string $url = null, ?string $name = null)
    {
        $this->name = $name;
        $this->url = $url;
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
    public function getUrl(): string
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
     * {@inheritdoc}
     *
     * @see \JsonSerializable::jsonSerialize()
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'class' => static::class,
            'name' => $this->name,
            'url' => $this->url,
        ];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, ?string $format = null, array $context = [])
    {
        $this->name = $data['name'];
        $this->url = $data['url'];
    }


}
