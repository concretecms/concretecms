<?php

namespace Concrete\Core\Health\Report\Finding\Message\Search;

use Concrete\Core\Entity\Attribute\Value\Value\Value;
use Concrete\Core\Health\Report\Finding\Message\Formatter\FormatterInterface;
use Concrete\Core\Health\Report\Finding\Message\Formatter\Search\BlockFormatter;
use Concrete\Core\Health\Report\Finding\Message\MessageInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class BlockMessage implements MessageInterface
{

    /**
     * @var int
     */
    protected $bID;

    /**
     * @var string
     */
    protected $content;

    public function __construct(?int $bID = null, ?string $content = null)
    {
        $this->bID = $bID;
        $this->content = $content;
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
            'bID' => $this->bID,
            'content' => $this->content,
        ];
        return $data;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return Value
     */
    public function getBlockID(): ?int
    {
        return $this->bID;
    }


    public function getFormatter(): FormatterInterface
    {
        return new BlockFormatter();
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, ?string $format = null, array $context = [])
    {
        $this->bID = $data['bID'];
        $this->content = $data['content'];
    }


}
