<?php

namespace Concrete\Core\Api\OpenApi\Parameter;

use Concrete\Core\Api\OpenApi\SpecParameter;
use Concrete\Core\Api\OpenApi\SpecSchema;

class AfterParameter extends SpecParameter
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Api\OpenApi\SpecParameter::getName()
     */
    public function getName(): string
    {
        return 'after';
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
        return t('The ID of the current object to start at.');
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
