<?php
namespace Concrete\Core\Announcement\Controller\Update;

use Concrete\Core\Announcement\Action\LearnMoreAction;
use Concrete\Core\Announcement\Action\VideoAction;
use Concrete\Core\Announcement\Button\LearnMoreButton;
use Concrete\Core\Announcement\Controller\AbstractController;
use Concrete\Core\Announcement\Icon\SvgIcon;
use Concrete\Core\Announcement\Item\StandardItem;
use Concrete\Core\Announcement\Slide\FeatureSlide;
use Concrete\Core\User\User;

class Version929Controller extends AbstractController
{

    public function getSlides(User $user): array
    {
        return [
            new FeatureSlide(
                t('Coming Soon'), [
                    new StandardItem(
                        t('Get ready for the new Marketplace'),
                        t('The next minor version of Concrete will connect to a new marketplace experience providing high-quality themes, add-ons, and SaaS integrations.'),
                    ),
                    new StandardItem(
                        t('Update your existing Add-ons & Themes Now'),
                        t('Take a quick look to apply any unapplied updates to your current extensions before May 15th.'),
                        [
                            new LearnMoreAction(DIR_REL . '/dashboard/extend/update')
                        ],
                    ),
                    new StandardItem(
                        t('9.2.9 Release Notes'),
                        t('Take a look at the changes that have been applied in the current version of Concrete.'),
                        [
                            new LearnMoreAction('https://documentation.concretecms.org/9-x/developers/introduction/version-history/929-release-notes')
                        ],
                    ),
                ],
                new LearnMoreButton('https://documentation.concretecms.org/9-x/developers/introduction/version-history/929-release-notes', t('View Release Notes'))
            )
        ];
    }


}
