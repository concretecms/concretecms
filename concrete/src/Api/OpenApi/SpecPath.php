<?php

namespace Concrete\Core\Api\OpenApi;

use Concrete\Core\Api\OpenApi\Parameter\ParameterInterface;

class SpecPath implements \JsonSerializable
{

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $tags;

    /**
     * @var string
     */
    protected $summary;

    /**
     * @var SpecParameter[]
     */
    protected $parameters = [];

    /**
     * @var SpecSecurity
     */
    protected $security;

    /**
     * @var SpecRequestBody
     */
    protected $requestBody;

    /**
     * @var SpecResponseList
     */
    protected $responses;

    /**
     * SpecBuilderPath constructor.
     * @param string $path
     * @param string $method
     */
    public function __construct(string $path, string $method, string $tags, string $summary = '')
    {
        $this->path = $path;
        $this->method = $method;
        $this->tags = $tags;
        $this->summary = $summary;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $method = strtolower($this->method);
        $data = [
            'path' => $this->path,
            $method => [
                'tags' => [$this->tags],
                'summary' => $this->summary,
                'parameters' => $this->parameters,
                'security' => $this->security,
                'responses' => $this->responses,
            ],
        ];
        if (isset($this->requestBody)) {
            $data[$method]['requestBody'] = $this->requestBody;
        }
        return $data;
    }

    public function addParameter(ParameterInterface $parameter)
    {
        $this->parameters[] = $parameter;
        return $this;
    }

    public function addResponse(SpecResponse $response)
    {
        if (!isset($this->responses)) {
            $this->responses = new SpecResponseList();
        }
        $this->responses->addResponse($response);
        return $this;
    }

    /**
     * @param SpecSecurity $security
     */
    public function setSecurity(SpecSecurity $security)
    {
        $this->security = $security;
        return $this;
    }

    /**
     * @param SpecRequestBody $requestBody
     */
    public function setRequestBody(SpecRequestBody $requestBody)
    {
        $this->requestBody = $requestBody;
        return $this;
    }




}
