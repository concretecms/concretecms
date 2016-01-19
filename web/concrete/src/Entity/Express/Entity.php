<?php
namespace Concrete\Core\Entity\Express;

use Concrete\Core\Attribute\EntityInterface;
use Concrete\Core\Express\Search\ColumnSet\ColumnSet;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="ExpressEntities")
 */
class Entity implements EntityInterface
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string")
     */
    protected $name;

    /**
     * @Column(type="text")
     */
    protected $description;

    /**
     * @Column(type="string")
     */
    protected $table_name;

    /**
     * @Column(type="object", nullable=true)
     */
    protected $result_column_set;

    /**
     * @OneToMany(targetEntity="\Concrete\Core\Entity\Attribute\Key\ExpressKey", mappedBy="entity", cascade={"persist", "remove"})
     **/
    protected $attributes;

    /**
     * @OneToMany(targetEntity="Association", mappedBy="source_entity", cascade={"persist", "remove"})
     **/
    protected $associations;

    /**
     * @OneToMany(targetEntity="Form", mappedBy="entity", cascade={"persist", "remove"})
     **/
    protected $forms;

    /**
     * @Column(type="datetime")
     */
    protected $created_date;

    public function __construct()
    {
        $this->created_date = new \DateTime();
        $this->attributes = new ArrayCollection();
        $this->forms = new ArrayCollection();
        $this->associations = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate()
    {
        return $this->created_date;
    }

    /**
     * @param mixed $created_date
     */
    public function setCreatedDate($created_date)
    {
        $this->created_date = $created_date;
    }

    /**
     * @return ColumnSet
     */
    public function getResultColumnSet()
    {
        return $this->result_column_set;
    }

    /**
     * @param mixed $result_column_set
     */
    public function setResultColumnSet($result_column_set)
    {
        $this->result_column_set = $result_column_set;
    }


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

    /**
     * @return ArrayCollection[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->table_name;
    }

    /**
     * @param mixed $table_name
     */
    public function setTableName($table_name)
    {
        $this->table_name = $table_name;
    }

    /**
     * @return mixed
     */
    public function getAssociations()
    {
        return $this->associations;
    }

    /**
     * @param mixed $associations
     */
    public function setAssociations($associations)
    {
        $this->associations = $associations;
    }

    /**
     * @return mixed
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * @param mixed $forms
     */
    public function setForms($forms)
    {
        $this->forms = $forms;
    }

    public function getAttributeKeyCategory()
    {
        return \Core::make('\Concrete\Core\Attribute\Category\ExpressCategory', array($this));
    }

    public function allowAttributeSets()
    {
        return false;
    }

    public function getAttributeSets()
    {
        return false;
    }
}
