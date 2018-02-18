<?php
namespace Concrete\Controller\Backend;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Validation\CSRF\Token;

class Queue extends AbstractController
{

    protected $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
        parent::__construct();
    }

    public function monitor($queue, $token)
    {
        if ($this->token->validate($queue, $token)) {

        }
        throw new \Exception(t('Access Denied'));
    }

}
