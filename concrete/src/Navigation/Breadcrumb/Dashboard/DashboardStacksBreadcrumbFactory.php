<?php
namespace Concrete\Core\Navigation\Breadcrumb\Dashboard;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Html\Service\Navigation;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Multilingual\Service\UserInterface\Flag;
use Concrete\Core\Navigation\Breadcrumb\BreadcrumbInterface;
use Concrete\Core\Navigation\Item\Item;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Stack\Folder\Folder;
use Concrete\Core\Page\Stack\Stack;

class DashboardStacksBreadcrumbFactory implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var DashboardBreadcrumbFactory
     */
    protected $breadcrumbFactory;

    /**
     * @var Navigation
     */
    protected $navigation;

    /**
     * @var bool
     */
    protected $displayGlobalAreasLandingPage = false;

    /**
     * @var \Concrete\Core\Multilingual\Page\Section\Section|null
     */
    private $currentSection;

    /**
     * Tell which language of which site the stack is being looked at in, for a language that has no
     * version of the stack yet: one that has says it itself.
     */
    public function setCurrentSection(?Section $section): void
    {
        $this->currentSection = $section;
    }

    /**
     * @param bool $displayGlobalAreasLandingPage
     */
    public function setDisplayGlobalAreasLandingPage(bool $displayGlobalAreasLandingPage): void
    {
        $this->displayGlobalAreasLandingPage = $displayGlobalAreasLandingPage;
    }

    public function __construct(DashboardBreadcrumbFactory $breadcrumbFactory, Navigation $navigation)
    {
        $this->breadcrumbFactory = $breadcrumbFactory;
        $this->navigation = $navigation;
    }

    public function getBreadcrumb(Page $dashboardPage, $stackOrFolder = null, ?array $sections = null, $locale = ''): BreadcrumbInterface
    {
        $breadcrumb = $this->breadcrumbFactory->getBreadcrumb($dashboardPage);
        $breadcrumb->setSanitizeName(false);
        if ($this->displayGlobalAreasLandingPage) {
            $breadcrumb->add(new Item(
                $this->app->make('url')->to('/dashboard/blocks/stacks', 'view_global_areas'),
                t('Global Areas')
            ));
        }
        if ($stackOrFolder) {
            if ($stackOrFolder instanceof Folder) {
                $stackOrFolder = $stackOrFolder->getPage();
            }
            $stacks = array_reverse($this->navigation->getTrailToCollection($stackOrFolder));
            array_shift($stacks); // Remove the top "Stacks" page.
            $currentSection = $this->currentSection;
            if ($stackOrFolder instanceof Stack && !$stackOrFolder->isNeutralStack()) {
                $currentSection = $stackOrFolder->getMultilingualSection() ?: $currentSection;
                $stackOrFolder = $stackOrFolder->getNeutralStack() ?: $stackOrFolder;
            }
            if ($currentSection === null && $locale !== '') {
                // what a caller that still says the language with its locale asks for
                $currentSection = $this->findSectionByLocale($sections ?? [], (string) $locale);
            }
            $stacks[] = $stackOrFolder;
            foreach($stacks as $stack) {
                $breadcrumb->add(new Item(
                    $this->app->make('url')->to(
                        '/dashboard/blocks/stacks', 'view_details', $stack->getCollectionID()
                    ),
                    $stack->getCollectionName()
                ));
            }
            if ($stackOrFolder instanceof Stack && !empty($sections)) {
                // a language of one site is told from the same language of another by the name of the site
                $withSiteNames = $this->countSitesOf($sections) > 1;
                if ($currentSection !== null) {
                    $multilingualMenuItem = new Item(
                        '',
                        $this->describeSection($currentSection, $withSiteNames, false),
                    );
                } else {
                    $multilingualMenuItem = new Item(
                        '',
                        h(tc('Locale', 'Default Content'))
                    );
                }
                $breadcrumb->add($multilingualMenuItem);

                $children = [
                    new Item(
                        $this->app->make('url')->to(
                            '/dashboard/blocks/stacks', 'view_details', $stackOrFolder->getCollectionID()
                        ),
                        '<strong>' . h(tc('Locale', 'Default Content')) . '</strong>',
                        $currentSection === null
                    )
                ];

                $customized = [];
                foreach ($stackOrFolder->getLocalizedStacks() as $localizedStack) {
                    $customized[(int) $localizedStack->getMultilingualSectionID()] = true;
                }
                foreach ($sections as $section) {
                    /** @var \Concrete\Core\Multilingual\Page\Section\Section $section */
                    $sectionID = (int) $section->getCollectionID();
                    $children[] = new Item(
                        $this->app->make('url')->to(
                            '/dashboard/blocks/stacks', 'view_details',
                            $stackOrFolder->getCollectionID() . rawurlencode('@' . $sectionID)
                        ),
                        $this->describeSection($section, $withSiteNames, isset($customized[$sectionID])),
                        $currentSection !== null && (int) $currentSection->getCollectionID() === $sectionID
                    );
                }

                foreach($children as $child) {
                    $multilingualMenuItem->addChild($child);
                }

            }
        }
        return $breadcrumb;
    }

    /**
     * @param \Concrete\Core\Multilingual\Page\Section\Section[] $sections
     *
     * @return \Concrete\Core\Multilingual\Page\Section\Section|null
     */
    private function findSectionByLocale(array $sections, string $locale)
    {
        foreach ($sections as $section) {
            if ($section->getLocale() === $locale) {
                return $section;
            }
        }

        return null;
    }

    /**
     * @param \Concrete\Core\Multilingual\Page\Section\Section[] $sections
     *
     * @return int how many sites the languages belong to
     */
    private function countSitesOf(array $sections): int
    {
        $siteIDs = [];
        foreach ($sections as $section) {
            $site = $section->getSite();
            if ($site !== null) {
                $siteIDs[$site->getSiteID()] = true;
            }
        }

        return count($siteIDs);
    }

    /**
     * @param bool $withSiteName whether the name of the site the language belongs to is told as well
     * @param bool $customized whether the stack has a version of its own in this language
     */
    private function describeSection(Section $section, bool $withSiteName, bool $customized): string
    {
        $name = Flag::getSectionFlagIcon($section) . ' ' . h($section->getLanguageText());
        if ($customized) {
            $name = '<strong>' . $name . '</strong>';
        }
        $details = (string) $section->getLocale();
        $site = $withSiteName ? $section->getSite() : null;
        if ($site !== null) {
            $details .= ', ' . $site->getSiteName();
        }

        return $name . ' <span class="text-muted">(' . h($details) . ')</span>';
    }
}
