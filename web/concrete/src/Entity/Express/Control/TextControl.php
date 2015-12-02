<?php

namespace Concrete\Core\Entity\Express\Control;

/**
 * @Entity
 * @Table(name="ExpressFormFieldSetTextControls")
 */
class TextControl extends Control
{
    /**
     * @Column(type="text")
     */
    protected $text;

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }




}