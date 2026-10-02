<?php

namespace Concrete\Core\Api\OpenApi\Parameter;

use Concrete\Core\Api\OpenApi\SpecParameter;
use Concrete\Core\Api\OpenApi\SpecSchema;

class LimitParameter extends SpecParameter
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\OpenApi\SpecParameter::getName()
     */
    public function getName(): string
    {
        return 'limit';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\OpenApi\SpecParameter::getIn()
     */
    public function getIn(): string
    {
        return 'query';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\OpenApi\SpecParameter::getDescription()
     */
    public function getDescription(): string
    {
        return t('The number of objects to return. Must be 100 or less. Defaults to 10.');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\OpenApi\SpecParameter::getSchema()
     */
    public function getSchema(): ?SpecSchema
    {
        return new SpecSchema('integer', 'int64');
    }


}
