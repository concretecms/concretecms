<?php
namespace Concrete\Controller\Permissions\Access\Entity;

use Concrete\Core\Controller\Controller;
use Concrete\Core\Permission\Access\Entity\Entity;
use Concrete\Core\Validation\CSRF\Token;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AccessEntity extends Controller
{

    /**
     * @var Token
     */
    protected $token;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Token $token)
    {
        $this->request = Request::createFromGlobals();
        $this->token = $token;
    }

    /**
     * @return Entity|null
     */
    abstract public function deliverEntity();

    public function getOrCreate()
    {
        $response = [];
        if ($this->token->validate('get_or_create')) {
            $entity = $this->deliverEntity();
            if ($entity) {
                $response['peID'] = $entity->getAccessEntityID();
                $response['label'] = $entity->getAccessEntityLabel();
            }
        }
        return new JsonResponse($response);
    }

}
