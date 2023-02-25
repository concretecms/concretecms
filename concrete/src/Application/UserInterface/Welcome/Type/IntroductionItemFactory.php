<?php

namespace Concrete\Core\Application\UserInterface\Welcome\Type;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Item\ArchitectureItem;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Item\BuildingAThemeItem;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Item\EcommerceItem;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Item\EditingBasicsItem;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Item\FindingYourLookItem;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Item\IntranetsAndPortalsItem;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\Item\PowerMovesItem;
use Concrete\Core\Application\UserInterface\Welcome\ContentItem\ItemInterface;
use Concrete\Core\SiteInformation\Question\BuildQuestion;
use Concrete\Core\SiteInformation\Question\RoleQuestion;
use Concrete\Core\SiteInformation\SiteInformationSurvey;

class IntroductionItemFactory implements ApplicationAwareInterface
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
     */
    public function getItems(): ?array
    {
        $config = $this->app->make('config/database');
        if ($config->get('app.site_information.viewed')) {
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
        return null;
    }


}
