<?php

namespace Concrete\Core\Announcement\Item\Factory;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Announcement\Item\Welcome\ArchitectureItem;
use Concrete\Core\Announcement\Item\Welcome\BuildingAThemeItem;
use Concrete\Core\Announcement\Item\Welcome\EcommerceItem;
use Concrete\Core\Announcement\Item\Welcome\EditingBasicsItem;
use Concrete\Core\Announcement\Item\Welcome\FindingYourLookItem;
use Concrete\Core\Announcement\Item\Welcome\IntranetsAndPortalsItem;
use Concrete\Core\Announcement\Item\Welcome\PowerMovesItem;
use Concrete\Core\Announcement\Item\ItemInterface;
use Concrete\Core\SiteInformation\Question\BuildQuestion;
use Concrete\Core\SiteInformation\Question\RoleQuestion;
use Concrete\Core\SiteInformation\SiteInformationSurvey;

class WelcomeItemFactory implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var SiteInformationSurvey
     */
    protected $survey;

    public function __construct(SiteInformationSurvey $survey)
    {
        $this->survey = $survey;
    }

    /**
     * @return ?ItemInterface[]
     * @var bool $returnNullIfSurveyUnfilled
     *
     * Gets items to display in the welcome screen. On the first load of the site, must return null
     * because we need to wait for the survey to be filled out, and the items will be filled by the c
     * component via AJAX to the server.
     *
     * However, in some remote possibilities (e.g. the super admin has not filled out the survey but
     * another admin has registered) we DO want to return the default set of options. Hence the option
     * here
     */
    public function getItems($returnNullIfSurveyUnfilled = false): ?array
    {
        $config = $this->app->make('config/database');
        if ($returnNullIfSurveyUnfilled && !$config->get('app.site_information.viewed')) {
            return null;
        }
        $items = [];
        // Slot 1: Let's always include the editing basics.
        $items[] = new EditingBasicsItem();

        $buildResult = $this->survey->getResult(BuildQuestion::class);
        if ($buildResult === BuildQuestion::HR_PORTAL || $buildResult === BuildQuestion::INTRANET) {
            $items[] = new IntranetsAndPortalsItem();
        } else {
            if ($buildResult === BuildQuestion::ECOMMERCE) {
                $items[] = new EcommerceItem();
            } else {
                $items[] = new FindingYourLookItem();
            }
        }

        $roleResult = $this->survey->getResult(RoleQuestion::class);
        if ($roleResult == RoleQuestion::DESIGNER) {
            $items[] = new BuildingAThemeItem();
        } else {
            if ($roleResult === RoleQuestion::PRODUCT_OWNER) {
                // We don't have this one working yet.
                // $items[] = new ShowSuccessItem();
            } else {
                if ($roleResult === RoleQuestion::DEVELOPER) {
                    $items[] = new ArchitectureItem();
                } else {
                    $items[] = new PowerMovesItem();
                }
            }
        }
        return $items;
    }


}
