<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\SummaryObject as BaseSummaryObject;

trait SummaryObjectCreatorTrait
{

    public function createSummaryContentObjects(CategoryMemberInterface $mixed) : array
    {
        $objects = [];
        if ($mixed->hasCustomSummaryTemplates()) {
            $this->logger->debug(t('Has custom summary templates, checking custom array.'));
            $templates = $mixed->getCustomSelectedSummaryTemplates();
        } else {
            $this->logger->debug(t('Does not have custom summary templates, checking base array.'));
            $templates = $mixed->getSummaryTemplates();
        }
        $this->logger->debug(t('%s summary templates retrieved for object %s - %s',
            count($templates),
            $mixed->getSummaryCategoryHandle(),
            $mixed->getSummaryIdentifier()
        ));

        foreach($templates as $template) {
            $objects[] = new SummaryObject(
                new BaseSummaryObject(
                    $mixed->getSummaryCategoryHandle(),
                    $mixed->getSummaryIdentifier(),
                    $template->getTemplate(),
                    $template->getData()
                )
            );

            $this->logger->debug(t('Creating summary content object for %s - %s with template %s and data %s',
                $mixed->getSummaryCategoryHandle(), $mixed->getSummaryIdentifier(),
                $template->getTemplate()->getName(), json_encode($template->getData())));
        }
        return $objects;
    }



}
