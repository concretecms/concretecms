<?php

namespace Concrete\Core\Api\OpenApi\Factory;

use Concrete\Core\Api\OpenApi\Parameter\Parameter;
use Concrete\Core\Api\OpenApi\SpecSchema;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Api\Attribute\OpenApiSpecifiableInterface;
use Concrete\Core\Api\OpenApi\JsonSchemaRefArrayContent;
use Concrete\Core\Api\OpenApi\JsonSchemaRefContent;
use Concrete\Core\Api\OpenApi\Parameter\AfterParameter;
use Concrete\Core\Api\OpenApi\Parameter\IncludesParameter;
use Concrete\Core\Api\OpenApi\Parameter\LimitParameter;
use Concrete\Core\Api\OpenApi\SpecComponents;
use Concrete\Core\Api\OpenApi\SpecFragment;
use Concrete\Core\Api\OpenApi\SpecModel;
use Concrete\Core\Api\OpenApi\SpecPath;
use Concrete\Core\Api\OpenApi\SpecProperty;
use Concrete\Core\Api\OpenApi\SpecPropertyRef;
use Concrete\Core\Api\OpenApi\SpecPropertyRefItems;
use Concrete\Core\Api\OpenApi\SpecRequestBody;
use Concrete\Core\Api\OpenApi\SpecResponse;
use Concrete\Core\Api\OpenApi\SpecSecurity;
use Concrete\Core\Entity\Express\ManyToOneAssociation;
use Concrete\Core\Entity\Express\OneToOneAssociation;
use Concrete\Core\Entity\Express\OneToManyAssociation;
use Concrete\Core\Entity\Express\ManyToManyAssociation;
use Concrete\Core\Api\OpenApi\SpecSecurityScheme;

class ExpressEntitySpecFactory
{

    const API_PREFIX = '/ccm/api/1.0';

    protected function addReadSchema(Entity $object)
    {
        $model = new SpecModel(camelcase($object->getHandle()), t('%s model', $object->getName()));
        $model
            ->addProperty(new SpecProperty('id', t('Entry public identifier'), 'string'))
            ->addProperty(new SpecProperty('date_added', t('Date Added'), 'string', 'date'))
            ->addProperty(new SpecProperty('date_last_updated', t('Date Last Updated'), 'string', 'date'))
            ->addProperty(new SpecProperty('label', t('Label'), 'string'))
            ->addProperty(new SpecProperty('url', t('URL'), 'string'))
            ->addProperty(
                new SpecProperty(
                    'author', t('Author'),
                    new SpecPropertyRef('/components/schemas/User')
                )
            );

        foreach ($object->getAttributes() as $attribute) {
            $model->addProperty(
                new SpecProperty(
                    $attribute->getAttributeKeyHandle(), $attribute->getAttributeKeyDisplayName(),
                    new SpecPropertyRef('/components/schemas/CustomAttribute')
                )
            );
        }

        foreach ($object->getAssociations() as $association) {
            if ($association instanceof ManyToOneAssociation || $association instanceof OneToOneAssociation) {
                $model->addProperty(
                    new SpecProperty(
                        $association->getTargetEntity()->getHandle(),
                        $association->getTargetEntity()->getName(),
                        new SpecPropertyRef(
                            '/components/schemas/' . camelcase($association->getTargetEntity()->getHandle())
                        )
                    )
                );
            } else {
                if ($association instanceof OneToManyAssociation || $association instanceof ManyToManyAssociation) {
                    $model->addProperty(
                        new SpecProperty(
                            $association->getTargetEntity()->getHandle(),
                            $association->getTargetEntity()->getName(),
                            'array',
                            null,
                            new SpecPropertyRefItems(
                                '/components/schemas/' . camelcase($association->getTargetEntity()->getHandle())
                            )
                        )
                    );
                }
            }
        }
        $components = new SpecComponents();
        $components->addModel($model);
        return $components;
    }

    protected function addCreateSchema(SpecComponents $components, Entity $object)
    {
        $modelName = 'New' . ucfirst(camelcase($object->getHandle()));
        $requestBody = new SpecRequestBody(
            $modelName,
            t('Creating a %s object', $object->getName())
        );

        $model = new SpecModel($modelName, t('%s model - New', $object->getName()));

        foreach ($object->getAttributes() as $attribute) {
            $controller = $attribute->getController();
            if ($controller instanceof OpenApiSpecifiableInterface) {
                $attributeProperty = $controller->getOpenApiSpecProperty($attribute);
            } else {
                $attributeProperty = new SpecProperty(
                    $attribute->getAttributeKeyHandle(),
                    $attribute->getAttributeKeyDisplayName(),
                    'string'
                );
            }

            $model->addProperty($attributeProperty);
        }

        foreach ($object->getAssociations() as $association) {
            if ($association instanceof ManyToOneAssociation || $association instanceof OneToOneAssociation) {
                $model->addProperty(
                    new SpecProperty(
                        $association->getTargetEntity()->getHandle(),
                        $association->getTargetEntity()->getName(),
                        'integer',
                    )
                );
            } else {
                if ($association instanceof OneToManyAssociation || $association instanceof ManyToManyAssociation) {
                    $model->addProperty(
                        new SpecProperty(
                            $association->getTargetEntity()->getPluralHandle(),
                            $association->getTargetEntity()->getName(),
                            'array',
                            null,
                            ['type' => 'integer'],
                        )
                    );
                }
            }
        }

        $components->addRequestBody($requestBody);
        $components->addModel($model);


        return $components;
    }

    protected function getIncludesForObject(Entity $object)
    {
        $includes = ['author'];
        foreach ($object->getAttributes() as $attribute) {
            $includes[] = $attribute->getAttributeKeyHandle();
        }
        foreach ($object->getAssociations() as $association) {
            $includes[] = $association->getTargetPropertyName();
        }
        return $includes;
    }

    protected function addList(SpecFragment $spec, Entity $object)
    {
        $handle = $object->getPluralHandle();
        $path = self::API_PREFIX . '/' . $handle;
        $includes = $this->getIncludesForObject($object);
        $spec->addPath(
            (new SpecPath(
                $path,
                'GET',
                $handle,
                t('Returns a list of %s objects, sorted by last updated descending.', $object->getName())
            ))
                ->setSecurity(new SpecSecurity('authorization', [$handle . ':read']))
                ->addParameter(new LimitParameter())
                ->addParameter(new AfterParameter())
                ->addParameter(new IncludesParameter($includes))
                ->addResponse(
                    new SpecResponse(
                        200,
                        t('An array of %s objects.', $object->getName()),
                        new JsonSchemaRefArrayContent('/components/schemas/' . camelcase($object->getHandle()))
                    )
                )
        );
        return $spec;
    }

    protected function addRead(SpecFragment $spec, Entity $object)
    {
        $handle = $object->getPluralHandle();
        $path = self::API_PREFIX . '/' . $handle . '/{uuid}';
        $includes = $this->getIncludesForObject($object);
        $spec->addPath(
            (new SpecPath(
                $path,
                'GET',
                $handle,
                t('Find a %s by its public identifier.', $object->getName())
            ))
                ->addParameter(
                    new Parameter('uuid', 'path', t('The public identifier/uuid of the entry.'), new SpecSchema('string', 'string'))
                )
                ->addParameter(new IncludesParameter($includes))
                ->setSecurity(new SpecSecurity('authorization', [$handle . ':read']))
                ->addResponse(
                    new SpecResponse(
                        200,
                        t('The %s object.', $object->getName()),
                        new JsonSchemaRefContent('/components/schemas/' . camelcase($object->getHandle()))
                    )
                )
        );
        return $spec;
    }

    protected function addCreate(SpecFragment $spec, Entity $object)
    {
        $handle = $object->getPluralHandle();
        $path = self::API_PREFIX . '/' . $handle;

        $specPath = (new SpecPath(
            $path,
            'POST',
            $handle,
            t('Add %s object.', $object->getName())
        ));

        $specPath
            ->setSecurity(new SpecSecurity('authorization', [$handle . ':add']))
            ->addResponse(
                new SpecResponse(
                    200,
                    t('The %s object.', $object->getName()),
                    new JsonSchemaRefContent('/components/schemas/' . camelcase($object->getHandle()))
                )
            );

        $specPath->setRequestBody(
            new SpecRequestBody('New' . ucfirst(camelcase($object->getHandle())))
        );

        $spec->addPath($specPath);
        return $spec;
    }

    protected function addUpdate(SpecFragment $spec, Entity $object)
    {
        $handle = $object->getPluralHandle();
        $path = self::API_PREFIX . '/' . $handle . '/{uuid}';

        $specPath = (new SpecPath(
            $path,
            'PUT',
            $handle,
            t('Updates %s object.', $object->getName())
        ));

        $specPath
            ->setSecurity(new SpecSecurity('authorization', [$handle . ':update']))
            ->addParameter(new Parameter('id', 'path', t('The ID of the object.'), new SpecSchema('string', 'string')))
            ->addResponse(
                new SpecResponse(
                    200,
                    t('The %s object.', $object->getName()),
                    new JsonSchemaRefContent('/components/schemas/' . camelcase($object->getHandle()))
                )
            );

        $specPath->setRequestBody(
            new SpecRequestBody('New' . ucfirst(camelcase($object->getHandle())))
        );

        $spec->addPath($specPath);
        return $spec;
    }

    protected function addDelete(SpecFragment $spec, Entity $object)
    {
        $handle = $object->getPluralHandle();
        $path = self::API_PREFIX . '/' . $handle . '/{uuid}';
        $spec->addPath(
            (new SpecPath(
                $path,
                'DELETE',
                $handle,
                t('Delete a %s.', $object->getName())
            ))
                ->addParameter(
                    new Parameter('id', 'path', t('The ID of the object.'), new SpecSchema('string', 'string'))
                )
                ->setSecurity(new SpecSecurity('authorization', [$handle . ':delete']))
                ->addResponse(
                    new SpecResponse(
                        200,
                        t('The %s object.', $object->getName()),
                        new JsonSchemaRefContent('/components/schemas/DeletedResponse')
                    )
                )
        );
        return $spec;
    }

    protected function addScopes(SpecFragment $spec, Entity $object)
    {
        $spec->addSecurityScheme(
            new SpecSecurityScheme(
                'authorization', [
                sprintf('%s:read', $object->getPluralHandle()) =>
                    t('Read %s information', $object->getName())
            ]
            ),
        );
        $spec->addSecurityScheme(
            new SpecSecurityScheme(
                'authorization', [
                sprintf('%s:add', $object->getPluralHandle()) =>
                    t('Add %s information', $object->getName())
            ]
            ),
        );
        $spec->addSecurityScheme(
            new SpecSecurityScheme(
                'authorization', [
                sprintf('%s:update', $object->getPluralHandle()) =>
                    t('Update %s information', $object->getName())
            ]
            ),
        );
        $spec->addSecurityScheme(
            new SpecSecurityScheme(
                'authorization', [
                sprintf('%s:delete', $object->getPluralHandle()) =>
                    t('Delete %s', $object->getName())
            ]
            ),
        );
        return $spec;
    }

    public function build(Entity $object)
    {
        $spec = new SpecFragment();
        $components = $this->addReadSchema($object);
        $components = $this->addCreateSchema($components, $object);
        $spec->setComponents($components);
        $this->addList($spec, $object);
        $this->addCreate($spec, $object);
        $this->addUpdate($spec, $object);
        $this->addRead($spec, $object);
        $this->addDelete($spec, $object);
        $this->addScopes($spec, $object);
        return $spec;
    }

}
