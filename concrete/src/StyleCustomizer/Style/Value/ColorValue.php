<?php

namespace Concrete\Core\StyleCustomizer\Style\Value;

class ColorValue extends Value
{
    /**
     * The value of the red channel.
     *
     * @var mixed
     */
    protected $r;

    /**
     * The value of the green channel.
     *
     * @var mixed
     */
    protected $g;

    /**
     * The value of the blue channel.
     *
     * @var mixed
     */
    protected $b;

    /**
     * The value of the alpha channel.
     *
     * @var mixed
     */
    protected $a;

    /**
     * Set the value of the red channel.
     *
     * @param mixed $r
     *
     * @return $this
     */
    public function setRed($r)
    {
        $this->r = $r;

        return $this;
    }

    /**
     * Set the value of the green channel.
     *
     * @param mixed $g
     *
     * @return $this
     */
    public function setGreen($g)
    {
        $this->g = $g;

        return $this;
    }

    /**
     * Set the value of the blue channel.
     *
     * @param mixed $b
     *
     * @return $this
     */
    public function setBlue($b)
    {
        $this->b = $b;

        return $this;
    }

    /**
     * Set the value of the alpha channel.
     *
     * @param mixed $a
     *
     * @return $this
     */
    public function setAlpha($a)
    {
        $this->a = $a;

        return $this;
    }

    /**
     * Get the value of the red channel.
     *
     * @return mixed
     */
    public function getRed()
    {
        return $this->r;
    }

    /**
     * Get the value of the green channel.
     *
     * @return mixed
     */
    public function getGreen()
    {
        return $this->g;
    }

    /**
     * Get the value of the blue channel.
     *
     * @return mixed
     */
    public function getBlue()
    {
        return $this->b;
    }

    /**
     * Get the value of the alpha channel.
     *
     * @return mixed
     */
    public function getAlpha()
    {
        return $this->a;
    }

    /**
     * Is the alpha channel set?
     *
     * @return bool
     */
    public function hasAlpha()
    {
        return (string) $this->a !== '';
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return [
            'r' => $this->getRed(),
            'g' => $this->getGreen(),
            'b' => $this->getBlue(),
            'a' => $this->getAlpha()
        ];
    }


}
