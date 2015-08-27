<?php
namespace Concrete\Core\Page\Type\PublishTarget\Configuration;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;

class AllConfiguration extends Configuration {

    protected $selectorFormFactor;

    public function canPublishPageTypeBeneathTarget(Type $pagetype, Page $page)
    {
        return true;
    }

    public function getSelectorFormFactor()
    {
        return $this->selectorFormFactor;
    }

    public function setSelectorFormFactor($selectorFormFactor)
    {
        $this->selectorFormFactor = $selectorFormFactor;
    }

}