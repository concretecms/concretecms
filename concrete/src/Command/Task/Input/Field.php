<?php
namespace Concrete\Core\Command\Task\Input;

use Symfony\Component\Serializer\Normalizer\DenormalizableInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class Field implements FieldInterface, DenormalizableInterface
{


    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $value;

    /**
     * AbstractField constructor.
     * @param string $key
     * @param string $value
     */
    public function __construct(string $key = null, string $value = null)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
    
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'key' => $this->getKey(),
            'value' => $this->getValue(),
        ];
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        $this->key = $data['key'];
        $this->value = $data['value'];
    }


}
