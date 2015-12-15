<?php

namespace Concrete\Core\Entity\AttributeKey;

use Concrete\Core\Attribute\Key\RequestLoader\TextareaRequestLoader;
use Concrete\Core\Entity\AttributeValue\TextareaAttributeValue;
use PortlandLabs\Concrete5\MigrationTool\Batch\Formatter\AttributeKey\TextAreaFormatter;
use PortlandLabs\Concrete5\MigrationTool\Publisher\AttributeKey\TextAreaPublisher;


/**
 * @Entity
 * @Table(name="TextareaAttributeKeys")
 */
class TextareaAttributeKey extends AttributeKey
{

    public function getTypeHandle()
    {
        return 'textarea';
    }

    public function getAttributeValue()
    {
        return new TextareaAttributeValue();
    }

    /**
     * @Column(type="string")
     */
    protected $mode = '';

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param mixed $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    public function getController()
    {
        $controller = new \Concrete\Attribute\Textarea\Controller($this->getAttributeType());
        return $controller;
    }

    public function getRequestLoader()
    {
        return new TextareaRequestLoader();
    }

}
