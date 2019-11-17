<?php

namespace Concrete\Core\Summary\Template;

class RendererFilterer
{

    public function getMatchingTemplate(array $allTemplates, array $customTemplates = null, string $templateHandle = null): ?RenderableTemplateInterface
    {
        
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
        
        if ($templateHandle) {
            foreach ($templatesToCheck as $template) {
                if ($template->getTemplate()->getHandle() === $templateHandle) {
                    return $template;
                }
            }
        } else {
            return $templatesToCheck[array_rand($templatesToCheck)];
        }
        return null;
    }

}
