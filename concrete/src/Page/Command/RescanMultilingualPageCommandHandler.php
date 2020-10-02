<?php

namespace Concrete\Core\Page\Command;

use Concrete\Core\Multilingual\Page\Section\Processor\ReplaceBlockPageRelationsTask;
use Concrete\Core\Multilingual\Page\Section\Processor\ReplaceContentLinksTask;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;

class RescanMultilingualPageCommandHandler
{

    protected function replaceBlockPageRelations(Page $c, Page $section)
    {
        $db = \Database::connection();
        $blocks = $c->getBlocks();
        $nvc = $c->getVersionToModify();
        $isApproved = $c->getVersionObject()->isApproved();
        foreach ($blocks as $b) {
            $controller = $b->getController();
            $pageColumns = $controller->getBlockTypeExportPageColumns();
            if (count($pageColumns)) {
                $columns = $db->MetaColumnNames($controller->getBlockTypeDatabaseTable());
                $data = array();
                $record = $controller->getBlockControllerData();
                foreach ($columns as $key) {
                    $data[$key] = $record->{$key};
                }

                foreach ($pageColumns as $column) {
                    $cID = $data[$column];
                    if ($cID > 0) {
                        $link = Page::getByID($cID, 'ACTIVE');
                        if (is_object($section)) {
                            $relatedID = $section->getTranslatedPageID($link);
                            if ($relatedID) {
                                $data[$column] = $relatedID;
                            }
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
        if ($isApproved) {
            $nvc->getVersionObject()->approve();
        }
    }

    public function replaceContentLinks(Page $c, Page $section)
    {
        $blocks = $c->getBlocks();
        $nvc = $c->getVersionToModify();
        $isApproved = $c->getVersionObject()->isApproved();
        foreach ($blocks as $b) {
            if ($b->getBlockTypeHandle() == 'content') {
                $content = $b->getController()->content;
                $content = preg_replace_callback(
                    '/{CCM:CID_([0-9]+)}/i',
                    function ($matches) {
                        $cID = $matches[1];
                        if ($cID > 0) {
                            $link = Page::getByID($cID, 'ACTIVE');
                            if (is_object($section)) {
                                $relatedID = $section->getTranslatedPageID($link);
                                if ($relatedID) {
                                    return sprintf('{CCM:CID_%s}', $relatedID);
                                }
                            }
                        }
                    },
                    $content);
                $ob = $b;
                // replace the block with the version of the block in the later version (if applicable)
                $b2 = \Block::getByID($b->getBlockID(), $nvc, $b->getAreaHandle());

                if ($b2->isAlias()) {
                    $nb = $ob->duplicate($nvc);
                    $b2->deleteBlock();
                    $b2 = clone $nb;
                }
                $data = array('content' => $content);
                $b2->update($data);
            }
        }
        if ($isApproved) {
            $nvc->getVersionObject()->approve();
        }
    }

    public function __invoke(RescanMultilingualPageCommand $command)
    {
        $page = Page::getByID($command->getPageID());
        if ($page && !$page->isError()) {
            $section = Section::getBySectionOfSite($page);
            $this->replaceBlockPageRelations($page, $section);
            $this->replaceContentLinks($page, $section);
        }
    }


}