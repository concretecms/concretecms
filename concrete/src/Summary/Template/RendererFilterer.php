<?php

namespace Concrete\Core\Summary\Template;

use Concrete\Core\Summary\Category\CategoryMemberInterface;

class RendererFilterer
{

    public function getSpecificTemplateIfExists(CategoryMemberInterface $object, string $templateHandle)
    {
        $templatesToCheck = $this->getTemplatesToCheck($object);
        foreach ($templatesToCheck as $template) {
            if ($template->getTemplate()->getHandle() === $templateHandle) {
                return $template;
            }
        }
    }

    protected function getTemplatesToCheck(CategoryMemberInterface $object)
    {
        $allTemplates = $object->getSummaryTemplates();
        $customTemplates = null;
        if ($object->hasCustomSummaryTemplates()) {
            $customTemplates = $object->getCustomSelectedSummaryTemplates();
        }

        $templatesToCheck = [];
        if ($customTemplates) {
            $customTemplateHandles = [];
            foreach($customTemplates as $customTemplate) {
                $customTemplateHandles[] = $customTemplate->getHandle();
            }
            foreach($allTemplates as $template) {
                if (in_array($template->getTemplate()->getHandle(), $customTemplateHandles)) {
                    $templatesToCheck[] = $template;
                }
            }
        } else {
            $templatesToCheck = $allTemplates;
        }

        return $templatesToCheck;
    }

    public function getRandomTemplate(CategoryMemberInterface $object): ?RenderableTemplateInterface
    {
        $templatesToCheck = $this->getTemplatesToCheck($object);
        return $templatesToCheck[array_rand($templatesToCheck)];
    }

}
