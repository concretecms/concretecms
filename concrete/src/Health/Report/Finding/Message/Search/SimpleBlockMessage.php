<?php

namespace Concrete\Core\Health\Report\Finding\Message\Search;

use Concrete\Core\Entity\Attribute\Value\Value\Value;
use Concrete\Core\Health\Report\Finding\Message\Formatter\FormatterInterface;
use Concrete\Core\Health\Report\Finding\Message\Formatter\Search\SimpleAttributeFormatter;
use Concrete\Core\Health\Report\Finding\Message\Formatter\Search\SimpleBlockFormatter;
use Concrete\Core\Health\Report\Finding\Message\MessageInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class SimpleBlockMessage implements MessageInterface
{

    /**
     * @var int
     */
    protected $bID;

    /**
     * @var string
     */
    protected $content;

    public function __construct(int $bID = null, string $content = null)
    {
        $this->bID = $bID;
        $this->content = $content;
    }


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


    /**
     * @return SimpleAttributeFormatter
     */
    public function getFormatter(): FormatterInterface
    {
        return new SimpleBlockFormatter();
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        $this->bID = $data['bID'];
        $this->content = $data['content'];
    }


}
