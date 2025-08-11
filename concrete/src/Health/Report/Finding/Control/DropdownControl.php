<?php
namespace Concrete\Core\Health\Report\Finding\Control;

use Concrete\Core\Health\Report\Finding\Control\Formatter\ButtonFormatter;
use Concrete\Core\Health\Report\Finding\Control\Formatter\DropdownFormatter;
use Concrete\Core\Health\Report\Finding\Control\Formatter\FormatterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DropdownControl implements ControlInterface
{

    /**
     * @var array
     */
    protected $controls = [];

    public function __construct(?array $controls = null)
    {
        $this->controls = $controls;
    }

    public function getFormatter(): FormatterInterface
    {
        return new DropdownFormatter();
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
            'controls' => $this->controls,
        ];
        return $data;
    }

    /**
     * @return ControlInterface[]
     */
    public function getControls(): ?array
    {
        return $this->controls;
    }

    public function denormalize(DenormalizerInterface $denormalizer, $data, ?string $format = null, array $context = [])
    {
        foreach ($data['controls'] as $control) {
            $this->controls[] = $denormalizer->denormalize($control, $control['class']);
        }
    }



}
