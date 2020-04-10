<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

class DashboardManager extends AbstractManager
{
    public function __construct()
    {
        $this->registerMessages([
            '/dashboard/composer/write' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Composer lets you create and publish pages (such as blog posts) directly from your Dashboard. At least one Page Type must be enabled for use Composer, and at least one block needs to be included in Composer via Page Type Defaults.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/composer/drafts' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('View content created using Composer, but have not yet published. '),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/sitemap/full' => implode('', [
                '<section class="ccm-panel-help-item">',
                '<h5>' . t("View and change your site's structure") . '</h5>',
                t('Click a page to see available actions; from this menu, you can control access to or delete a page. Click and drag a page to move it to a different place in your site.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/sitemap/explore' => implode('', [
                '<section class="ccm-panel-help-item">',
                '<h5>' . t('View a single branch of your Sitemap') . '</h5>',
                t('Useful for working with large sites and complex tree structures.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/sitemap/search' => implode('', [
                '<section class="ccm-panel-help-item">',
                '<h5>' . t('Search for pages by name and type') . '</h5>',
                t('Perform actions on multiple pages simultaneously by checking the boxes next to the pages you want to perform the action on, then selecting the action you want to perform from the drop-down menu.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/files/search' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("Upload, search, replace, and change files you're using on your site. Change properties and perform basic image editing tasks."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/files/attributes' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Categorize file attributes into sets to give greater organizational context to editors and enhance ease of use.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/files/sets' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("File sets provide a flexible way to organize and group your site's assets."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/users/search' => implode('', [
                '<section class="ccm-panel-help-item">',
                '<h5>' . t('Search for users by name, email, and group') . '</h5>',
                t('Perform actions on multiple users simultaneously by checking the boxes next to the users you want to perform the action on, then selecting the action you want to perform from the drop-down menu.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/users/groups' => implode('', [
                '<section class="ccm-panel-help-item">',
                '<h5>' . t('View, search for, add, and edit groups') . '</h5>',
                t('Click a group to edit it. Groups are useful for organizing users and setting permissions for multiple users simultaneously.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/users/add_group' => implode('', [
                '<section class="ccm-panel-help-item">',
                '<h5>' . t('Create a new group to organize and classify users') . '</h5>',
                t('Choose an expiration date to remove users automatically.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/reports/forms' => implode('', [
                '<section class="ccm-panel-help-item">',
                '<h5>' . t("View responses to your site's forms") . '</h5>',
                t('Submitted form data is collected here.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/reports/surveys' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('The results of completed survey blocks are logged here. Find out what your users have submitted.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/reports/logs' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('View a record of errors your site has encountered, and a log of outgoing email messages sent by your site.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/pages/themes' => implode('', [
                '<section class="ccm-panel-help-item">',
                '<h5>' . t("Themes change the overall design of your site's pages") . '</h5>',
                t("Activating a theme will apply it to every page in your site. Preview allows you to see how your content will look when the new theme is applied. Inspect lists the Page Types a theme uses. Customize allows you to change certain properties of your theme's styles, if allowed."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/pages/types' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("Page types display a page's blocks in different ways and can be thought of as 'templates' for your content: Left Sidebar, Full, etc. Page types correspond to specific themes. concrete5 will look for the specified page type in your active theme."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/pages/attributes' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Control the types of information that can be saved about each page in your site.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/pages/single' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Single Pages are used when you want to run custom code in the concrete5 environment. Each Single Page is available in only one location in your sitemap, and this location must be registered from this Dashboard page. This location corresponds to a PHP file placed in your single_pages/ override directory. Below is a list of Single Pages currently installed.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/blocks/stacks' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Stacks are groups of blocks, and make it easier to recycle content places on many pages of your site. Stacks are edited and administered from the Dashboard, then placed on individual pages or deployed as Page Type Defaults. Block content and display order can be controlled, as well as the ability to roll-back to previous versions of the stack.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/blocks/types' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("All blocks currently installed on your site are listed here. Custom blocks that you've developed will appear under Awaiting Installation' until you install them. Click any block to find information about usage and refreshing a block's database tables."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/environment/logging' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('View emails your site sends, as well as error messages PHP and MySQL errors returned as your site runs.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/environment/debug' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Choose between having error messages appear on the page where they occur (as you might want to do while developing a site) and hiding them from site visitors by saving them in your log (as you might want to do if your site is active).'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/environment/info' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("View information about your concrete5 site's configuration, as well as details about the hosting environment you're using."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/attributes/types' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Select which types of attributes to make available to pages, users, and files.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/attributes/sets' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Attributes are available to pages (aka Collections), users and files. If you want to add a new attribute to any of these objects, start here.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/mail/importers' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('These scripts look for incoming mail to your site. Add-ons like the concrete5 Discussion Forum will attach emails to posts if they match, creating a new page containing the message as reply text.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/mail/method' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Set concrete5 to use an external SMTP server, instead of the default PHP Mail function, for sending email.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/permissions/blacklist' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Block users at specific IP addresses from logging into your site. Blocked users can still view pages that are visible to guests.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/permissions/tasks' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("Control users' ability to do perform specific tasks, such as install packages, alter permissions, etc."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/permissions/file_types' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('View and change which file types you permit users to upload to your File Manager.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/permissions/files' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("Control how users interact with your site's File Manager, allowing or disallowing actions like search, upload, replace and more."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/permissions/trusted_proxies' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('If your website uses a reverse proxy (like <a href="%s" target="_blank">Cloudflare</a>), you have to specify here the list of the IP addresses of the proxy, so that concrete5 can detect the actual IP address of the visitors.', 'https://www.cloudflare.com/ips/'),
                '<br /><br />',
                t('The checked headers will be trusted only when PHP detects that the connection is made via a trusted proxy.'),
                '<br /><br />',
                t(/*%s is the name of an HTTP header*/'The %s header should be selected when using RFC 7239.', '<code>FORWARDED</code>'),
                '<br /><br />',
                t('The other headers starting with %1$s are not standard but are widely used by popular reverse proxies (like %2$s).', '<code>X_...</code>', 'Apache mod_proxy/Amazon EC2'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/basics/editor' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("Control which set of tools the content-editor toolbar includes (e.g., Simple, Advanced, Office), and the toolbar's spatial dimensions."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/basics/multilingual' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('View available language packs installed for the concrete5 Dashboard and editing interface.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/basics/multilingual/update' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Install new language files and update the outdated ones.<br />You can contribute to translations on %s', '<a href="https://translate.concrete5.org" target="_blank">translate.concrete5.org</a>.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/basics/timezone' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Allow your users to specify their time zone. This setting is editable in the user profile and in the dashboard users section.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/basics/icons' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Upload an image that will appear in the address bar when visitors go to your site and in the bookmark list if they bookmark it.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/basics/name' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("Even if you change your website's logo, the Site Name is used in some emails."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/registration/open' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Enable Public Registration to let visitors create new user accounts.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/registration/profiles' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("Display information about your concrete5 site's users, on a public page."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/registration/postlogin' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Determine where users should be redirected to after they login.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/environment/storage' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("Create an alternate file-storage location (in addition to the standard file location) where you'll have the option of putting files after uploading them to the File Manager. "),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/update/update' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Download the latest version of concrete5 and upgrade your site.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/permissions/maintenance_mode' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Enable or disable maintenance mode, in which your site is only visible to the admin user. Maintenance Mode is useful for developing, testing or temporarily disabling a site.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/optimization/jobs' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Have concrete5 perform various tasks to help your site running in top condition, process email posts, and update search engine indexing maps. Click the triangle icon next to the job to start it. A success message will be displayed once the job has been completed.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/optimization/clearcache' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("If your site is behaving oddly or displaying out-of-date content, it's a good idea to clear the cache. If you're having to clear the cache a lot, you might want to just turn off caching in Cache & Speed Settings."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/seo/urls' => [
                implode('', [
                    '<section class="ccm-panel-help-item">',
                    t("Remove index.php from your URLs with pretty URLs, and ensure canonical URLs if you're running a site at multiple domains."),
                    '</section class="ccm-panel-help-item">',
                ]),
                'dashboard-system-urls',
            ],
            '/dashboard/system/seo/codes' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("Add any HTML or Javascript code you need for analytics tracking to every page of your site, and pick whether it will go in pages' header or footer. This is where you would input code from Google Analytics, for example."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/seo/statistics' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("Turns tracking of page views, file downloads and user registrations on or off. These are displayed on your site's Dashboard > Reports > Statistics page. If your high-traffic site experiences performance issues, you might consider disabling statistics tracking and investigate the use of an alternate, third-party solution for tracking site stats."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/permissions/site' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("Control basic, general parameters for viewing and editing your site. Viewing Permissions makes your site's pages accessible to all users, registered-users-only or administrators-only. Edit Access controls which groups can edit pages, when logged in. For more granular control, set permissions on pages individually from Page Properties, or enable Advanced Permissions for even more granular control."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/composer/edit' => implode('', [
                '<section class="ccm-panel-help-item">',
                t("Any attributes or block areas you have enabled to be editable in Composer for this page type are available here. Add blocks to your page type's defaults. After adding the block, click it and choose the option to make it available in Composer."),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/users/attributes' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Store data about your users-- like site preferences, birthdays, bios and more. Control which elements are available for users to update themselves.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/users/add' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Manually create new user accounts for your concrete5 site.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('The Dashboard allows you to perform administrative tasks for your site.'),
                '</section class="ccm-panel-help-item">',
            ]),
            '/dashboard/system/files/image_uploading' => implode('', [
                '<section class="ccm-panel-help-item">',
                t('Control maximum dimensions for all images uploaded to your website. Ensures that enormous images will be resized.') . ' ' . t('Auto-rotate images accordingly to EXIF metadata.') . '<br />' . t('Set PNG and JPEG compression options.'),
                '</section class="ccm-panel-help-item">',
            ]),
        ]);
    }
}
