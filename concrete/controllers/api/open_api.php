<?php
namespace Concrete\Controller\Api;

use Concrete\Core\Api\OpenApi\SpecGenerator;
use Concrete\Core\Permission\Checker;
use Symfony\Component\HttpFoundation\Response;

class OpenApi
{

    /**
     * @var SpecGenerator
     */
    protected $specGenerator;

    /**
     * @param SpecGenerator $specGenerator
     */
    public function __construct(SpecGenerator $specGenerator)
    {
        $this->specGenerator = $specGenerator;
    }

    public function generate()
    {
        $checker = new Checker();
        if ($checker->canAccessApi()) {
            $r = $this->specGenerator->getSpec();
            return new Response($r->toYaml());
        } else {
            throw new \UserMessageException(t('Access Denied.'));
        }
    }

}
