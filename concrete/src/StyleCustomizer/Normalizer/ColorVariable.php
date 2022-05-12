<?php

namespace Concrete\Core\StyleCustomizer\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;

class ColorVariable implements VariableInterface
{

    protected $name;

    protected $r;

    protected $g;

    protected $b;

    protected $a = 1;

    /**
     * NumberVariable constructor.
     * @param $r
     * @param $g
     * @param $b
     * @param $a
     */
    public function __construct($name, $r, $g, $b, $a = 1)
    {
        $this->name = $name;
        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
        $this->a = $a;
        if ($a === null) {
            $this->a = 1;
        }
    }

    /**
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getRed()
    {
        return $this->r;
    }

    /**
     * @return mixed
     */
    public function getGreen()
    {
        return $this->g;
    }

    /**
     * @return mixed
     */
    public function getBlue()
    {
        return $this->b;
    }

    /**
     * @return int|mixed
     */
    public function getAlpha()
    {
        return $this->a;
    }

    public function getValue()
    {
        if ($this->getAlpha() === 1) {
            $value = sprintf('rgb(%s, %s, %s)', $this->getRed(), $this->getGreen(), $this->getBlue());
        } else {
            $value = sprintf('rgba(%s, %s, %s, %s)', $this->getRed(), $this->getGreen(), $this->getBlue(), $this->getAlpha());
        }
        return $value;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'type' => 'color',
            'name' => $this->getName(),
            'r' => $this->getRed(),
            'g' => $this->getGreen(),
            'b' => $this->getBlue(),
            'a' => $this->getAlpha(),
        ];
    }


}
