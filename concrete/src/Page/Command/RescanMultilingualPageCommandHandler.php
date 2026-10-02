<?php

declare(strict_types=1);

namespace Concrete\Core\Page\Command;

use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;

defined('C5_EXECUTE') or die('Access Denied.');

class RescanMultilingualPageCommandHandler
{
    public function __invoke(RescanMultilingualPageCommand $command)
    {
        $page = Page::getByID($command->getPageID());
        if (!$page || $page->isError()) {
            return;
        }
        $section = $this->getSection($page);
        if (!$section) {
            return;
        }
        $isApproved = $page->getVersionObject()->isApproved();
        $nvc = $page->getVersionToModify();
        $this->replaceBlockPageRelations($page, $nvc, $section);
        $this->replaceContentLinks($page, $nvc, $section);
        if ($isApproved) {
            $nvc->getVersionObject()->approve();
        }
    }

    /**
     * Stacks live outside the site trees, so they resolve their section through the Stacks table.
     */
    protected function getSection(Page $page): ?Section
    {
        if ($page->getPageTypeHandle() === STACKS_PAGE_TYPE) {
            $stack = Stack::getByID($page->getCollectionID());

            return $stack ? $stack->getMultilingualSection() : null;
        }
        $section = Section::getBySectionOfSite($page);

        return $section ?: null;
    }

    protected function replaceBlockPageRelations(Page $c, Page $nvc, Section $section)
    {
        $db = \Database::connection();
        foreach ($c->getBlocks() as $b) {
            $controller = $b->getController();
            $pageColumns = $controller->getBlockTypeExportPageColumns();
            if (count($pageColumns)) {
                $columns = $db->MetaColumnNames($controller->getBlockTypeDatabaseTable());
                $data = [];
                $record = $controller->getBlockControllerData();
                foreach ($columns as $key) {
                    $data[$key] = $record->{$key};
                }

                foreach ($pageColumns as $column) {
                    $cID = $data[$column];
                    if ($cID > 0) {
                        $link = Page::getByID($cID, 'ACTIVE');
                        $relatedID = $section->getTranslatedPageID($link);
                        if ($relatedID) {
                            $data[$column] = $relatedID;
                        }
                    }
                }

                unset($data['bID']);

                $ob = $b;
                // replace the block with the version of the block in the later version (if applicable)
                $b2 = \Block::getByID($b->getBlockID(), $nvc, $b->getAreaHandle());
                if ($b2->isAlias()) {
                    $nb = $ob->duplicate($nvc);
                    $b2->deleteBlock();
                    $b2 = clone $nb;
                }
                $b2->update($data);
            }
        }
    }

    protected function replaceContentLinks(Page $c, Page $nvc, Section $section)
    {
        foreach ($c->getBlocks() as $b) {
            if ($b->getBlockTypeHandle() == 'content') {
                /** @var \Concrete\Block\Content\Controller $controller */
                $controller = $b->getController();
                $content = $controller->content;
                $content = preg_replace_callback(
                    '/{CCM:CID_([0-9]+)}/i',
                    static function ($matches) use ($section) {
                        $cID = (int) $matches[1];
                        if ($cID > 0) {
                            $link = Page::getByID($cID, 'ACTIVE');
                            $relatedID = $section->getTranslatedPageID($link);
                            if ($relatedID) {
                                return sprintf('{CCM:CID_%s}', $relatedID);
                            }
                        }

                        // Leave links to untranslated pages alone rather than stripping them.
                        return $matches[0];
                    },
                    $content
                );
                $ob = $b;
                // replace the block with the version of the block in the later version (if applicable)
                $b2 = \Block::getByID($b->getBlockID(), $nvc, $b->getAreaHandle());
                if ($b2->isAlias()) {
                    $nb = $ob->duplicate($nvc);
                    $b2->deleteBlock();
                    $b2 = clone $nb;
                }
                $b2->update(['content' => $content]);
            }
        }
    }
}
