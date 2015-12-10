<?php

namespace Concrete\Core\Entity\AttributeKey;

use Concrete\Core\Attribute\Key\RequestLoader\TextareaRequestLoader;
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

    public function getFieldMappingDefinition()
    {
        return array('type' => 'text', 'options' => array('length' => 4294967295, 'default' => null, 'notnull' => false));
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
        return new \Concrete\Attribute\Textarea\Controller($this->getAttributeType());
    }

    public function getRequestLoader()
    {
        return new TextareaRequestLoader();
    }

}
