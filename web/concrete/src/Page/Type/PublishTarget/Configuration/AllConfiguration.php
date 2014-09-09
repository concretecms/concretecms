<?php
namespace Concrete\Core\Page\Type\PublishTarget\Configuration;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Type\Type;

class AllConfiguration extends Configuration {

    public function canPublishPageTypeBeneathTarget(Type $pagetype, Page $page)
    {
        return true;
    }
}