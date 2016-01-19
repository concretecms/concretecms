<?php
namespace Concrete\Core\Express;

use Concrete\Core\Attribute\AttributeKeyMappingFieldBuilder;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Express\FieldBuilder\PrimaryKeyFieldBuilder;
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
        $primaryKey = new PrimaryKeyFieldBuilder();
        $primaryKey->build($builder);
        $attributes = $this->entity->getAttributes();
        foreach ($attributes as $key) {
            /** @var $key \Concrete\Core\Entity\Attribute\Key\Key */
            $fieldBuilder = new AttributeKeyMappingFieldBuilder($key);
            $fieldBuilder->build($builder);
        }
        $associations = $this->entity->getAssociations();
        foreach ($associations as $association) {
            $associationBuilder = $association->getAssociationBuilder();
            $associationBuilder->build($builder);
        }
    }
}
