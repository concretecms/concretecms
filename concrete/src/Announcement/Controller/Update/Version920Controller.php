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

class Version920Controller extends AbstractController
{

    public function getSlides(User $user): array
    {
        return [
            new FeatureSlide(
                t('New in 9.2: Major Features'), [
                    new StandardItem(
                        t('Full REST API'),
                        t('Concrete CMS can now power your custom applications with a built-in, standards-compliant REST API.'),
                        [],
                        new SvgIcon('<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path d="M16 0c-8.823 0-16 7.177-16 16s7.177 16 16 16c8.823 0 16-7.177 16-16s-7.177-16-16-16zM16 1.527c7.995 0 14.473 6.479 14.473 14.473s-6.479 14.473-14.473 14.473c-7.995 0-14.473-6.479-14.473-14.473s6.479-14.473 14.473-14.473zM11.161 7.823c-0.188-0.005-0.375 0-0.568 0.005-1.307 0.079-2.093 0.693-2.312 1.964-0.151 0.891-0.125 1.796-0.188 2.692-0.020 0.464-0.067 0.928-0.156 1.38-0.177 0.813-0.525 1.068-1.353 1.109-0.111 0.011-0.22 0.032-0.324 0.057v1.948c1.5 0.073 1.704 0.605 1.823 2.172 0.048 0.573-0.015 1.147 0.021 1.719 0.027 0.543 0.099 1.079 0.208 1.6 0.344 1.432 1.745 1.911 3.433 1.624v-1.713c-0.272 0-0.511 0.005-0.74 0-0.579-0.016-0.792-0.161-0.844-0.713-0.079-0.713-0.057-1.437-0.099-2.156-0.089-1.339-0.235-2.651-1.541-3.5 0.672-0.495 1.161-1.084 1.312-1.865 0.109-0.547 0.177-1.099 0.219-1.651s-0.025-1.12 0.021-1.667c0.077-0.885 0.135-1.249 1.197-1.213 0.161 0 0.317-0.021 0.495-0.036v-1.745c-0.213 0-0.411-0.005-0.604-0.011zM21.287 7.839c-0.365-0.011-0.729 0.016-1.089 0.079v1.697c0.329 0 0.584 0 0.833 0.005 0.439 0.005 0.772 0.177 0.813 0.661 0.041 0.443 0.041 0.891 0.083 1.339 0.089 0.896 0.136 1.796 0.292 2.677 0.136 0.724 0.636 1.265 1.255 1.713-1.088 0.729-1.411 1.776-1.463 2.953-0.032 0.801-0.052 1.615-0.093 2.427-0.037 0.74-0.297 0.979-1.043 0.995-0.208 0.011-0.411 0.027-0.64 0.041v1.74c0.432 0 0.833 0.027 1.235 0 1.239-0.073 1.995-0.677 2.239-1.885 0.104-0.661 0.167-1.333 0.183-2.005 0.041-0.615 0.036-1.235 0.099-1.844 0.093-0.953 0.532-1.349 1.484-1.411 0.089-0.011 0.177-0.032 0.267-0.057v-1.953c-0.161-0.021-0.271-0.037-0.391-0.041-0.713-0.032-1.068-0.272-1.251-0.948-0.109-0.433-0.177-0.876-0.197-1.324-0.052-0.823-0.047-1.656-0.099-2.479-0.109-1.588-1.063-2.339-2.516-2.38zM12.099 14.875c-1.432 0-1.536 2.109-0.115 2.245h0.079c0.609 0.036 1.131-0.427 1.167-1.037v-0.061c0.011-0.62-0.484-1.136-1.104-1.147zM15.979 14.875c-0.593-0.020-1.093 0.448-1.115 1.043 0 0.036 0 0.067 0.005 0.104 0 0.672 0.459 1.099 1.147 1.099 0.677 0 1.104-0.443 1.104-1.136-0.005-0.672-0.459-1.115-1.141-1.109zM19.927 14.875c-0.624-0.011-1.145 0.485-1.167 1.115 0 0.625 0.505 1.131 1.136 1.131h0.011c0.567 0.099 1.135-0.448 1.172-1.104 0.031-0.609-0.521-1.141-1.152-1.141z"/></svg>')
                    ),
                    new StandardItem(
                        t('Site Health'),
                        t('Check your site for optimal performance and security.'),
                        [
                            new VideoAction('https://www.youtube.com/watch?v=K76xk1E6hPE')
                        ],
                        new SvgIcon('<svg width="100%" height="100%" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" color="#000000"><path d="M21 8v8a5 5 0 01-5 5H8a5 5 0 01-5-5V8a5 5 0 015-5h8a5 5 0 015 5z" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M13.9 18h-3.8a.6.6 0 01-.6-.6v-2.3a.6.6 0 00-.6-.6H6.6a.6.6 0 01-.6-.6v-3.8a.6.6 0 01.6-.6h2.3a.6.6 0 00.6-.6V6.6a.6.6 0 01.6-.6h3.8a.6.6 0 01.6.6v2.3a.6.6 0 00.6.6h2.3a.6.6 0 01.6.6v3.8a.6.6 0 01-.6.6h-2.3a.6.6 0 00-.6.6v2.3a.6.6 0 01-.6.6z" stroke="#000000" stroke-width="1.5"></path></svg>')
                    ),
                    new StandardItem(
                        t('Updated In-Page Editing'),
                        t('Much better drag and drop performance and refined area display.'),
                        [
                            new LearnMoreAction('https://www.loom.com/share/066801cd6d3748dbad14ae1fa6a56919')
                        ],
                        new SvgIcon('<svg width="100%" height="100%" viewBox="0 0 24 24" stroke-width="1.5" fill="none" xmlns="http://www.w3.org/2000/svg" color="#000000"><path d="M14.363 5.652l1.48-1.48a2 2 0 012.829 0l1.414 1.414a2 2 0 010 2.828l-1.48 1.48m-4.243-4.242l-9.616 9.615a2 2 0 00-.578 1.238l-.242 2.74a1 1 0 001.084 1.085l2.74-.242a2 2 0 001.24-.578l9.615-9.616m-4.243-4.242l4.243 4.242" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>')
                    ),
                ],
                new LearnMoreButton('https://documentation.concretecms.org/developers/introduction/version-history/920-release-notes', t('View Release Notes'))
            ),
            new FeatureSlide(
                t('New in 9.2: Updates'), [
                   new StandardItem(
                       t('More Features'),
                       t('Production Modes, Failed Transport Display, Email address management improvements and much more.'),
                       [],
                       new SvgIcon('<svg width="100%" height="100%" viewBox="0 0 24 24" stroke-width="1.5" fill="none" xmlns="http://www.w3.org/2000/svg" color="#000000"><path d="M3 21l10-10m5-5l-2.5 2.5" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M9.5 2l.945 2.555L13 5.5l-2.555.945L9.5 9l-.945-2.555L6 5.5l2.555-.945L9.5 2zM19 10l.54 1.46L21 12l-1.46.54L19 14l-.54-1.46L17 12l1.46-.54L19 10z" stroke="#000000" stroke-width="1.5" stroke-linejoin="round"></path></svg>')
                   ),
                   new StandardItem(
                       t('Refinements'),
                       t('Select-picker overhaul, performance improvements and multilingual improvements.'),
                       [],
                       new SvgIcon('<svg width="100%" height="100%" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" color="#000000"><path d="M20.51 9.54a1.899 1.899 0 01-1 1.09A7 7 0 0015.37 17c.001.47.048.939.14 1.4a2.16 2.16 0 01-.31 1.65 1.79 1.79 0 01-1.21.8 9 9 0 01-10.62-9.13A9.05 9.05 0 0111.85 3h.51a9 9 0 018.06 5 2 2 0 01.09 1.52v.02z" stroke="#000000" stroke-width="1.5"></path><path d="M8 16.01l.01-.011M6 12.01l.01-.011M8 8.01l.01-.011M12 6.01l.01-.011M16 8.01l.01-.011" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>')
                   ),
                   new StandardItem(
                       t('Bug Fixes'),
                       t('Over 50 bug fixes and improved PHP8 compatibility.'),
                       [],
                       new SvgIcon('<svg width="100%" height="100%" viewBox="0 0 24 24" stroke-width="1.5" fill="none" xmlns="http://www.w3.org/2000/svg" color="#000000"><g clip-path="url(#wrench_svg__clip0_2576_14436)" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.05 10.607l-7.07 7.07a2 2 0 000 2.83v0a2 2 0 002.828 0l7.07-7.072M10.05 10.607c-.844-2.153-.679-4.978 1.06-6.718 1.74-1.74 4.95-2.121 6.718-1.06l-3.04 3.04-.283 3.111 3.111-.282 3.04-3.041c1.062 1.768.68 4.978-1.06 6.717-1.74 1.74-4.564 1.905-6.717 1.061"></path></g><defs><clipPath id="wrench_svg__clip0_2576_14436"><path fill="#fff" d="M0 0h24v24H0z"></path></clipPath></defs></svg>')
                   ),
               ],
                new LearnMoreButton('https://documentation.concretecms.org/developers/introduction/version-history/920-release-notes', t('View Release Notes'))
            ),
        ];
    }


}
