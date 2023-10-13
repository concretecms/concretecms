<?php

namespace Concrete\Core\Health\Report\Finding\Message\Search;

use Concrete\Core\Entity\Attribute\Value\AbstractValue;
use Concrete\Core\Entity\Attribute\Value\Value\Value;
use Concrete\Core\Health\Report\Finding\Message\Formatter\FormatterInterface;
use Concrete\Core\Health\Report\Finding\Message\Formatter\Search\AttributeFormatter;
use Concrete\Core\Health\Report\Finding\Message\MessageInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AttributeMessage implements MessageInterface
{

    /**
     * @var Value
     */
    protected $value;

    public function __construct(Value $value = null)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     *
     * @see \JsonSerializable::jsonSerialize()
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [
            'class' => static::class,
            'value' => $this->value->getAttributeValueID(),
        ];
        return $data;
    }

    public function getCategoryValue(): ?AbstractValue
    {
        if ($this->value) {
            $key = $this->value->getAttributeKey();
            if ($key) {
                $value = $key->getAttributeCategory()->getAttributeValueRepository()->findOneBy(
                    ['generic_value' => $this->value]
                );
                if ($value instanceof AbstractValue) {
                    return $value;
                }
            }
        }
        return null;
    }

    public function getDetails(): string
    {
        return $this->getCategoryValue()->getDisplayValue();
    }

    /**
     * @return Value
     */
    public function getValue(): ?Value
    {
        return $this->value;
    }

    /**
     * @return AttributeFormatter
     */
    public function getFormatter(): FormatterInterface
    {
        return new AttributeFormatter();
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        $em = app(EntityManager::class);
        $this->value = $em->find(Value::class, $data['value']);
    }


}
