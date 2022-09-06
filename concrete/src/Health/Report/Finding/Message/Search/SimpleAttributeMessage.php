<?php

namespace Concrete\Core\Health\Report\Finding\Message\Search;

use Concrete\Core\Entity\Attribute\Value\AbstractValue;
use Concrete\Core\Entity\Attribute\Value\Value\Value;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Health\Report\Finding\Message\Formatter\FormatterInterface;
use Concrete\Core\Health\Report\Finding\Message\Formatter\Search\SimpleAttributeFormatter;
use Concrete\Core\Health\Report\Finding\Message\MessageInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class SimpleAttributeMessage implements MessageInterface
{

    /**
     * @var Value
     */
    protected $value;

    public function __construct(Value $value = null)
    {
        $this->value = $value;
    }


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
        $key = $this->value->getAttributeKey();
        if ($key) {
            $value = $key->getAttributeCategory()->getAttributeValueRepository()->findOneBy(
                ['generic_value' => $this->value]
            );
            if ($value instanceof AbstractValue) {
                return $value;
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
     * @return SimpleAttributeFormatter
     */
    public function getFormatter(): FormatterInterface
    {
        return new SimpleAttributeFormatter();
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        $em = app(EntityManager::class);
        $this->value = $em->find(Value::class, $data['value']);
    }


}
