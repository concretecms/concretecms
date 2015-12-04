<?php

namespace Concrete\Core\Entity\AttributeKey;

use Concrete\Core\Attribute\Type;
use Concrete\Core\Entity\PackageTrait;
use Concrete\Core\Express\Definition\Factory;
use Concrete\Core\Express\Field\DefinitionFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use PortlandLabs\Concrete5\MigrationTool\Batch\Formatter\AttributeKey\BlankFormatter;
use PortlandLabs\Concrete5\MigrationTool\Entity\Import\Batch;
use PortlandLabs\Concrete5\MigrationTool\Publisher\PublishableInterface;
use PortlandLabs\Concrete5\MigrationTool\Publisher\Validator\AttributeKeyValidator;


/**
 * @Entity
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="type", type="string")
 * @Table(name="AttributeKeys")
 */
abstract class AttributeKey
{

    use PackageTrait;

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string")
     */
    protected $handle;

    /**
     * @Column(type="string")
     */
    protected $name;

    /**
     * @Column(type="boolean")
     */
    protected $is_searchable = true;

    /**
     * @Column(type="boolean")
     */
    protected $is_internal = false;

    /**
     * @Column(type="boolean")
     */
    protected $is_indexed = false;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param mixed $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @return mixed
     */
    public function getIsInternal()
    {
        return $this->is_internal;
    }

    /**
     * @param mixed $is_internal
     */
    public function setIsInternal($is_internal)
    {
        $this->is_internal = $is_internal;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDisplayName()
    {
        return $this->getName();
    }

    /**
     * @return mixed
     */
    public function getIsSearchable()
    {
        return $this->is_searchable;
    }

    /**
     * @param mixed $is_searchable
     */
    public function setIsSearchable($is_searchable)
    {
        $this->is_searchable = $is_searchable;
    }

    /**
     * @return mixed
     */
    public function getIsIndexed()
    {
        return $this->is_indexed;
    }

    /**
     * @param mixed $is_indexed
     */
    public function setIsIndexed($is_indexed)
    {
        $this->is_indexed = $is_indexed;
    }

    /**
     * @return
     */
    abstract public function getFieldMappingDefinition();

    abstract public function getType();

    /**
     * @deprecated
     */
    public function render($view = 'view', $value = false, $return = false)
    {
        $at = Type::getByHandle($this->getType());
        $resp = $at->render($view, $this, $value, $return);
        if ($return) {
            return $resp;
        } else {
            print $resp;
        }
    }


}
