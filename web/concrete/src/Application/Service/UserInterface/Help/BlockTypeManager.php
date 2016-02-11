<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

class BlockTypeManager extends AbstractManager
{

    public function __construct()
    {
        $this->registerMessages(array(
            'autonav' => array(t("Create a navigational menu that reflects the structure of your Sitemap. First, choose the order in which pages appear. Viewing Permissions checks a user's permissions before rendering the link for each page. Display Pages selects the level of the Sitemap where you'd like the menu to begin. Options for displaying sub-pages for each item are also available."), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/auto-nav'),
            'content' => array(t('Add text content and stylize it using the WYSIWYG editor toolbar. Create links to pages, files and other site assets by using the upper concrete5 toolbar.'), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/content'),
            'date_archive' => array(t('Display a list of pages created during a certain month, or months. Pages will be sorted by month.'), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/date-navigation'),
            'date_nav' => array(t('Display a list of pages that use a certain page type. Return pages that exist throughout your site, or under only one specific section.'), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/date-navigation'),
            'external_form' => array(t('Select a custom-coded form to display as a block on your page.'), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/external-form'),
            'file' => array(t('Choose a file from the File Manager and the File block will create a hyperlink to it using the link text you specify.'), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/file'),
            'form' => array(t('To begin creating a form, type your question text into the text field, choose the type of answer you need, and whether or not a question response is required when submitting the form. Click Add, then repeat for subsequent questions.'), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/form'),
            'google_map' => array(t("Enter a title for your map, then the address of the location you'd like to display on your map. Finally, specify the zoom level of the map to render. Google will try to locate the address automatically when you add the block."), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/google-maps'),
            'guestbook' => array(t("Enter a title for your guestbook, and adjust the date format to your liking. Choose whether or not to enable comments, moderation and CAPTCHA. Enter an email address if you'd like to be notified of each new guestbook submission."), 'http://www.concrete5.org/documentation/using-concrete5/in-page-editing/block-areas/add-block/guestbook-comments/'),
            'html' => array(t('Paste your HTML code into this field, and it will be rendered by your web browser.'), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/html'),
            'image' => array(t('Select an image from the File Manager, and optionally select a rollover image. Choose where to link the image, if desired, and enter alt text. Use Constrain Image Dimensions to force the image to be displayed at a different size than the actual image file.'), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/image'),
            'next_previous' => array(t('This block creates links to adjacent pages on the same level of the Sitemap as the current page. Define custom label text for each link, and choose whether or not to display arrows. The Loop option will display the first page again when a user reaches the last page in the nav.'), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/next-and-previous-nav'),
            'page_list' => array(t("The Page List creates a navigation menu that shows one particular level of the Sitemap. Select the Sitemap location that you'd like to display and set Sorting Options to define the order in which pages will be displayed. Truncate Summaries will shorten Page Description text to a specified number of characters."), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/page-list'),
            'rss_displayer' => array(t('Paste a link to an external RSS feed located on another site, and concrete5 will render it on your page. Select date formatting, feed title, number of items to display at once and choose whether to show or hide article summaries.'), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/rss-displayer'),
            'search' => array(t('Create a block to allow users to search the content of your concrete5 site. Choose title, button text, and where concrete5 should search. To submit the form to another page, choose another page from the Sitemap. Place a second Search block on this page and the results will appear here.'), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/search'),
            'slideshow' => array(t('Add individual images selected from the File Manager, or choose an existing file set. Playback options allow you to display images in order or randomly. concrete5 will render the images as an animated slideshow.'), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/image-slider'),
            'survey' => array(t('Add questions and specify whether or not unregistered users will be allowed to submit responses. Enter each response as its own option under Add Option. Results can be viewed by visiting Dashboard > Reports > Surveys. '), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/survey'),
            'tags' => array(t('Create a tag cloud that displays all the "Tag" custom attributes set on the current page, or on all pages throughout your site. Enter values into the "Tags" field to automatically add tags to the current page. Link the tags to a specific page by clicking the Advanced tab and using the page picker to select a page.'), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/tags'),
            'video' => array(t('Select a video file from the File Manager and specify a width and height at which to display it on your page. AVI, WMV, QuickTime/MPEG4 and FLV formats are supported.'), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/video-player'),
            'youtube' => array(t('Paste a short or long-form YouTube link into the YouTube URL field and concrete5 will embed the video on your page. Playlist URLs and comma seperated lists of video IDs are also supported.'), 'http://documentation.concrete5.org/editors/in-page-editing/block-areas/add-block/youtube-video')
        ));
    }

    public function registerMessages($messages)
    {
        foreach($messages as $identifier => $message) {
            $m = new Message();
            $m->setIdentifier($identifier);
            $m->setMessageContent($message[0]);
            if ($message[1]) {
                $m->addLearnMoreLink($message[1]);
            }

            $this->messages[$identifier] = $m;
        }

    }

}