<?php
namespace Concrete\Core\Attribute;

use Concrete\Core\Attribute\Category\CategoryInterface;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Type as TypeEntity;
use Concrete\Core\Validation\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

interface ValidatorInterface
{

    /**
     * @param CategoryInterface $category
     * @param Type $type
     * @param Request $request
     * @return ResponseInterface
     */
    function validateAddKeyRequest(CategoryInterface $category, TypeEntity $type, Request $request);

    /**
     * @param CategoryInterface $category
     * @param Key $key
     * @param Request $request
     * @return ResponseInterface
     */
    function validateUpdateKeyRequest(CategoryInterface $category, Key $key, Request $request);

    /**
     * @param Key $key
     * @param Request $request
     * @return ResponseInterface
     */
    function validateSaveValueRequest(Key $key, Request $request);

}