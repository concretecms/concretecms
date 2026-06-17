<?php
namespace Concrete\Core\Announcement\Controller\Update;

use Concrete\Core\Announcement\Action\LearnMoreAction;
use Concrete\Core\Announcement\Button\LearnMoreButton;
use Concrete\Core\Announcement\Controller\AbstractController;
use Concrete\Core\Announcement\Icon\ImgIcon;
use Concrete\Core\Announcement\Item\StandardItem;
use Concrete\Core\Announcement\Slide\FeatureSlide;
use Concrete\Core\User\User;

class Version930Controller extends AbstractController
{

    public function getSlides(User $user): array
    {
        return [
            new FeatureSlide(
                t('The New Marketplace is Here!'), [
                    new StandardItem(
                        t('Brand New!'),
                        t('Tailored for website owners, developers, and creative professionals alike, our new marketplace offers high-quality themes, add-ons, and integrations to SaaS platforms to help you build content driven web applications with ease.'),
                        [],
                        new ImgIcon('https://www.concretecms.com/application/files/1716/2430/7644/Concrete5_Illustration_14.png'),
                    ),
                    new StandardItem(
                        t('Free Demos on SaaS Hosting'),
                        t('The Concrete CMS Marketplace offers a new "Try Before You Buy" feature, allowing users to take extensions for a trial run on our SaaS hosting before committing to a purchase.'),
                        [],
                        new ImgIcon('https://www.concretecms.com/application/files/5117/1500/8306/free_source.svg'),
                    ),
                ],
                new LearnMoreButton('https://www.concretecms.com/about/new-marketplace', t('Learn More'))
            )
        ];
    }


}
