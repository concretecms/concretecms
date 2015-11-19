<?php

namespace Concrete\Core\Page\Type\PublishTarget\Type;

use Concrete\Core\Page\Type\Type as PageType;
use Concrete\Core\Page\Type\PublishTarget\Configuration\AllConfiguration;

class AllType extends Type
{
    public function configurePageTypePublishTarget(PageType $pt, $post)
    {
        $configuredTarget = new AllConfiguration($this);
        $configuredTarget->setSelectorFormFactor(isset($post['selectorFormFactorAll']) ? $post['selectorFormFactorAll'] : null);
        $configuredTarget->setStartingPointPageID(isset($post['startingPointPageIDAll']) ? $post['startingPointPageIDAll'] : null);

        return $configuredTarget;
    }

    public function configurePageTypePublishTargetFromImport($txml)
    {
        $configuration = new AllConfiguration($this);
        $formFactor = (string) $txml['form-factor'];
        if ($formFactor) {
            $configuration->setSelectorFormFactor($formFactor);
        }
        $path = (string) $txml['path'];
        if ($path) {
            $c = \Page::getByPath($path);
            $configuration->setStartingPointPageID($c->getCollectionID());
        }
        return $configuration;
    }
}
