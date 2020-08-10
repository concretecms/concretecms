<?php
namespace Concrete\Core\Navigation\Breadcrumb\Dashboard;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Html\Service\Navigation;
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

    public function getBreadcrumb(Page $dashboardPage, $stackOrFolder = null, array $sections = null, $locale = ''): BreadcrumbInterface
    {
        $breadcrumb = $this->breadcrumbFactory->getBreadcrumb($dashboardPage);
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
            $stacks[] = $stackOrFolder;
            foreach($stacks as $stack) {
                $stackItem = new Item(
                    $this->app->make('url')->to(
                        '/dashboard/blocks/stacks', 'view_details', $stack->getCollectionID()
                    ),
                    $stack->getCollectionName()
                );
                $breadcrumb->add($stackItem);
            }
            if ($stackOrFolder instanceof Stack) {
                // let's handle the breadcrumb dropdown for localized stacks
                if ($stackOrFolder->isNeutralStack()) {
                    $neutralStack = $stackOrFolder;
                } else {
                    $neutralStack = $stackOrFolder->getNeutralStack();
                }

                $children = [
                    new Item(
                        $this->app->make('url')->to(
                            '/dashboard/blocks/stacks', 'view_details', $neutralStack->getCollectionID()
                        ),
                        '<strong>' . h(tc('Locale', 'Default Content')) . '</strong>',
                        $locale === ''
                    )
                ];

                foreach ($sections as $sectionLocale => $section) {
                    /* @var Section $section */
                    $localizedStackName = Flag::getSectionFlagIcon($section) . ' ' . h($section->getLanguageText());
                    if ($neutralStack->getLocalizedStack($section) !== null) {
                        $localizedStackName = '<strong>' . $localizedStackName . '</strong>';
                    }
                    $children[] = new Item(
                        $this->app->make('url')->to(
                            '/dashboard/blocks/stacks', 'view_details',
                            $neutralStack->getCollectionID() . rawurlencode('@' . $sectionLocale
                            )
                        ),
                        $localizedStackName . ' <span class="text-muted">' . h($sectionLocale) . '</span>',
                        $locale === $sectionLocale
                    );
                }

                foreach($children as $child) {
                    $stackItem->addChild($child);
                }

            }
        }
        return $breadcrumb;
    }
}
