<?php
namespace Concrete\Core\Site\Type;

use Concrete\Core\Application\UserInterface\OptionsForm\OptionsFormProviderInterface;
use Concrete\Core\Entity\Site\Type;

class OptionsFormProvider implements OptionsFormProviderInterface
{
    protected $type;

    public function __construct(Type $type)
    {
        $this->type = $type;
    }

    public function getPackageHandle()
    {
        return $this->type->getPackageHandle();
    }

    public function getElementController()
    {
        return '\\Controller\\Element\\SiteType\\Form\\' . camelcase($this->type->getSiteTypeHandle());
    }

    public function getType()
    {
        return $this->type;
    }

}
