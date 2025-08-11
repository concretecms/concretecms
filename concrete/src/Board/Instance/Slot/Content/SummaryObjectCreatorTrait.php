<?php
namespace Concrete\Core\Board\Instance\Slot\Content;

use Concrete\Core\Board\Instance\Logger\Logger;
use Concrete\Core\Board\Instance\Logger\LoggerInterface;
use Concrete\Core\Summary\Category\CategoryMemberInterface;
use Concrete\Core\Summary\SummaryObject as BaseSummaryObject;

trait SummaryObjectCreatorTrait
{

    public function createSummaryContentObjects(CategoryMemberInterface $mixed, LoggerInterface $logger) : array
    {
        $objects = [];
        if ($mixed->hasCustomSummaryTemplates()) {
            $logger->write(t('Has custom summary templates, checking custom array.'));
            $templates = $mixed->getCustomSelectedSummaryTemplates();
        } else {
            $logger->write(t('Does not have custom summary templates, checking base array.'));
            $templates = $mixed->getSummaryTemplates();
        }
        $logger->write(t('%s summary templates retrieved for object %s - %s',
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

            $logger->write(t('Creating summary content object for %s - %s with template %s',
                $mixed->getSummaryCategoryHandle(), $mixed->getSummaryIdentifier(),
                $template->getTemplate()->getName()
            ), $template->getData());
        }
        return $objects;
    }



}
