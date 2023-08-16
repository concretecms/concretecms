<?php
namespace Concrete\Core\Health\Report\Finding\Message;

use Concrete\Core\Health\Report\Finding\Message\Formatter\FormatterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Concrete\Core\Health\Report\Finding\Message\Formatter\MessageFormatter;

class Message implements MessageInterface
{

    /**
     * @var string
     */
    protected $message;

    public function __construct(string $message = null)
    {
        $this->message = $message;
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
            'message' => $this->message,
        ];
        return $data;
    }

    /**
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getFormatter(): FormatterInterface
    {
        return new MessageFormatter();
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, string $format = null, array $context = [])
    {
        $this->message = $data['message'];
    }




}
