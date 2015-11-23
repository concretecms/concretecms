<?php

namespace Concrete\Core\Express;

use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\Definition\PrimaryKeyField;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

class MetadataPopulator
{

    protected $metadata;
    protected $entity;
    protected $table_prefix;

    public function __construct(ClassMetadata $metadata, Entity $entity)
    {
        $this->metadata = $metadata;
        $this->entity = $entity;
    }

    /**
     * @return mixed
     */
    public function getTablePrefix()
    {
        return $this->table_prefix;
    }

    /**
     * @param mixed $table_prefix
     */
    public function setTablePrefix($table_prefix)
    {
        $this->table_prefix = $table_prefix;
    }

    public function populate()
    {
        $table = $this->getTablePrefix() . $this->entity->getTableName();
        $builder = new ClassMetadataBuilder($this->metadata);
        $builder->setTable($table);
        $columns = array(new PrimaryKeyField());
        $attributes = $this->entity->getAttributes();
        foreach($attributes as $attribute) {
            /** @var $key \Concrete\Core\Entity\AttributeKey\AttributeKey */
            $key = $attribute->getAttribute();
            $fields = $key->getDefinitionFields();
            foreach($fields as $field) {
                $columns[] = $field;
            }
        }

        foreach($columns as $field) {
            $builder->addField($field->getName(), $field->getType(), $field->getOptions());
        }
    }


}