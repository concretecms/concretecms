<?php

namespace Concrete\Core\Backup\ContentImporter\Importer\Routine;

use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Stack;
use SimpleXMLElement;

class ImportStacksContentRoutine extends AbstractPageContentRoutine implements SpecifiableHomePageRoutineInterface
{
    use StackTrait;

    /**
     * @var \Concrete\Core\Page\Page|null
     */
    protected $home;

    /**
     * @var \Concrete\Core\Entity\Site\Site|null
     */
    private $site;

    public function getHandle()
    {
        return 'stacks_content';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\Importer\Routine\SpecifiableHomePageRoutineInterface::setHomePage()
     */
    public function setHomePage($page)
    {
        $this->home = $page;
    }

    public function import(SimpleXMLElement $sx)
    {
        if (!isset($sx->stacks)) {
            return;
        }
        if (!$this->home || $this->home->isError()) {
            $this->home = Page::getByID(Page::getHomePageID(), 'RECENT');
        }
        if (!$this->home || $this->home->isError()) {
            $this->site = null;
        } else {
            $this->site = $this->home->getSite();
        }
        foreach ($sx->stacks->stack as $p) {
            $stack = $this->getStack($p);
            $locale = isset($p['section']) ? (string) $p['section'] : '';
            if ($locale !== '') {
                $section = Section::getByLocale($locale, $this->site);
                if (!$section) {
                    continue;
                }
                $localizedStack = $stack->getLocalizedStack($section);
                $stack = $localizedStack ?: $stack->addLocalizedStack($section, ['copyContents' => false]);
            }
            if (isset($p->area)) {
                $this->importPageAreas($stack, $p);
            }
        }
    }

    /**
     * @return \Concrete\Core\Page\Stack\Stack
     */
    private function getStack(SimpleXMLElement $stackElement)
    {
        $name = (string) $stackElement['name'];
        $type = (string) $stackElement['type'];
        if ($type === 'global_area') {
            return Stack::getByName($name, 'RECENT', $this->site);
        }
        $folder = $this->getOrCreateFolderByPath('/' . trim((string) $stackElement['path'], '/'));
        $stackID = $this->getStackIDByName($name, $folder);

        return Stack::getByID($stackID);
    }
}
