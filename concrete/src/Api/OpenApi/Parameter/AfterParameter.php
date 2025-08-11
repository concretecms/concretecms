<?php

namespace Concrete\Core\Api\OpenApi\Parameter;

use Concrete\Core\Api\OpenApi\SpecParameter;
use Concrete\Core\Api\OpenApi\SpecSchema;

class AfterParameter extends SpecParameter
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'after';
    }

    /**
     * @return string
     */
    public function getIn(): string
    {
        return 'query';
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return t('The ID of the current object to start at.');
    }

    public function getSchema(): ?SpecSchema
    {
        return new SpecSchema('integer', 'int64');
    }


}
