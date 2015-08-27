<?php
namespace Concrete\Core\Page\Type\PublishTarget\Type;

use Loader;
use Concrete\Core\Page\Type\Type as PageType;
use \Concrete\Core\Page\Type\PublishTarget\Configuration\AllConfiguration;

class AllType extends Type
{

    public function configurePageTypePublishTarget(PageType $pt, $post)
    {
        $configuredTarget = new AllConfiguration($this);
        $configuredTarget->setSelectorFormFactor($post['selectorFormFactorAll']);
        return $configuredTarget;
    }

    public function configurePageTypePublishTargetFromImport($txml)
    {
        $configuration = new AllConfiguration($this);
        $formFactor = (string) $txml['form-factor'];
        if ($formFactor) {
            $configuration->setSelectorFormFactor($formFactor);
        }
        return $configuration;
    }




}