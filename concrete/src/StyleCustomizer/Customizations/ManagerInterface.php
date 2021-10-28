<?php
namespace Concrete\Core\StyleCustomizer\Customizations;

use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Foundation\Command\Command;

interface ManagerInterface
{

    /**
     * Detects whether your site has theme-level customizations applied. Only really applicable for the legacy
     * customizer.
     *
     * @param Site $site
     * @return bool
     */
    public function hasSiteThemeCustomizations(Site $site): bool;

    /**
     * Detects whether individual pages have theme customizations applied, either through the application of a
     * custom skin, or in the case of the legacy customizer, the application of a custom style set.
     *
     * @param Site $site
     * @return bool
     */
    public function hasPageThemeCustomizations(Site $site): bool;

    /**
     * @param Site $site
     * @return mixed
     */
    public function getResetSiteThemeCustomizationsCommand(Site $site);

    /**
     * @param Site $site
     * @return mixed
     */
    public function getResetPageThemeCustomizationsCommand(Site $site);





}
