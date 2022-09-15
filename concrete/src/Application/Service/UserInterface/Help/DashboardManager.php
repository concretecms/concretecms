<?php
namespace Concrete\Core\Application\Service\UserInterface\Help;

class DashboardManager extends AbstractManager
{
    public function __construct()
    {
        $this->registerMessages([
            '/dashboard/composer/write' => implode('', [
                '<p>' . t('Composer lets you create and publish pages (such as blog posts) directly from your Dashboard. At least one Page Type must be enabled for use Composer, and at least one block needs to be included in Composer via Page Type Defaults.') . '</p>',
            ]),
            '/dashboard/composer/drafts' => implode('', [
                '<p>' . t('View content created using Composer, but have not yet published. ') . '</p>',
            ]),
            '/dashboard/sitemap/full' => implode('', [
                '<h1>' . t("View and change your site's structure") . '</h1>',
                '<p>' . t('Click a page to see available actions; from this menu, you can control access to or delete a page. Click and drag a page to move it to a different place in your site.') . '</p>',
            ]),
            '/dashboard/sitemap/explore' => implode('', [
                '<h1>' . t('View a single branch of your Sitemap') . '</h1>',
                '<p>' . t('Useful for working with large sites and complex tree structures.') . '</p>',
            ]),
            '/dashboard/sitemap/search' => implode('', [
                '<h1>' . t('Search for pages by name and type') . '</h1>',
                '<p>' . t('Perform actions on multiple pages simultaneously by checking the boxes next to the pages you want to perform the action on, then selecting the action you want to perform from the drop-down menu.') . '</p>',
            ]),
            '/dashboard/files/search' => implode('', [
                '<p>' . t("Upload, search, replace, and change files you're using on your site. Change properties and perform basic image editing tasks.") . '</p>',
            ]),
            '/dashboard/files/attributes' => implode('', [
                '<p>' . t('Categorize file attributes into sets to give greater organizational context to editors and enhance ease of use.') . '</p>',
            ]),
            '/dashboard/files/sets' => implode('', [
                '<p>' . t("File sets provide a flexible way to organize and group your site's assets.") . '</p>',
            ]),
            '/dashboard/users/search' => implode('', [
                '<h1>' . t('Search for users by name, email, and group') . '</h1>',
                '<p>' . t('Perform actions on multiple users simultaneously by checking the boxes next to the users you want to perform the action on, then selecting the action you want to perform from the drop-down menu.') . '</p>',
            ]),
            '/dashboard/users/groups' => implode('', [
                '<h1>' . t('View, search for, add, and edit groups') . '</h1>',
                '<p>' . t('Click a group to edit it. Groups are useful for organizing users and setting permissions for multiple users simultaneously.') . '</p>',
            ]),
            '/dashboard/users/add_group' => implode('', [
                '<h1>' . t('Create a new group to organize and classify users') . '</h1>',
                '<p>' . t('Choose an expiration date to remove users automatically.') . '</p>',
            ]),
            '/dashboard/reports/forms' => implode('', [
                '<h1>' . t("View responses to your site's forms") . '</h1>',
                '<p>' . t('Submitted form data is collected here.') . '</p>',
            ]),
            '/dashboard/reports/surveys' => implode('', [
                '<p>' . t('The results of completed survey blocks are logged here. Find out what your users have submitted.') . '</p>',
            ]),
            '/dashboard/reports/logs' => implode('', [
                '<p>' . t('View a record of errors your site has encountered, and a log of outgoing email messages sent by your site.') . '</p>',
            ]),
            '/dashboard/pages/themes' => implode('', [
                '<h1>' . t("Themes change the overall design of your site's pages") . '</h1>',
                '<p>' . t("Activating a theme will apply it to every page in your site. Preview allows you to see how your content will look when the new theme is applied. Inspect lists the Page Types a theme uses. Customize allows you to change certain properties of your theme's styles, if allowed.") . '</p>',
                '<h5>' . t("Page Types & Templates") . '</h5>',
                '<p class="text-muted">' . t("Learn more about how the relationship between the page types you use throughout your site and their templates.") . '</p>',
                '<div class="ccm-panel-help-media"><div><a href="https://www.youtube.com/watch?v=1LZW_e4AUA8" data-lightbox="iframe"><i class="fas fa-play-circle"></i> ' . t("Watch Video") . '</a></div></div>',
            ]),
            '/dashboard/pages/types' => implode('', [
                '<h1>' . t('Page Types') . '</h1>',
                '<p>' . t("Page types allow you to refer to your pages as the type of object they represent. Examples of page types include 'Basic Page', 'Blog Entry', 'Document List', etc...") . '</p>',
                '<h5>' . t("Page Types & Templates") . '</h5>',
                '<p class="text-muted">' . t("Learn more about how the relationship between the page types you use throughout your site and their templates.") . '</p>',
                '<div class="ccm-panel-help-media"><div><a href="https://www.youtube.com/watch?v=1LZW_e4AUA8" data-lightbox="iframe"><i class="fas fa-play-circle"></i> ' . t("Watch Video") . '</a></div></div>',
            ]),
            '/dashboard/pages/templates' => implode('', [
                '<h1>' . t('Page Templates') . '</h1>',
                '<p>' . t("Most pages in Concrete CMS have a template. Page templates map directly to code files in your theme, which determine how your page appears. Examples of page templtaes include 'Left Sidebar', 'Full Width', etc...") . '</p>',
                '<h5>' . t("Page Types & Templates") . '</h5>',
                '<p class="text-muted">' . t("Learn more about how the relationship between the page types you use throughout your site and their templates.") . '</p>',
                '<div class="ccm-panel-help-media"><div><a href="https://www.youtube.com/watch?v=1LZW_e4AUA8" data-lightbox="iframe"><i class="fas fa-play-circle"></i> ' . t("Watch Video") . '</a></div></div>',
            ]),
            '/dashboard/pages/attributes' => implode('', [
                '<p>' . t('Control the types of information that can be saved about each page in your site.') . '</p>',
            ]),
            '/dashboard/pages/single' => implode('', [
                '<p>' . t('Single Pages are used when you want to run custom code in the concrete5 environment. Each Single Page is available in only one location in your sitemap, and this location must be registered from this Dashboard page. This location corresponds to a PHP file placed in your single_pages/ override directory. Below is a list of Single Pages currently installed.') . '</p>',
            ]),
            '/dashboard/blocks/stacks' => implode('', [
                '<p>' . t('Stacks are groups of blocks, and make it easier to recycle content places on many pages of your site. Stacks are edited and administered from the Dashboard, then placed on individual pages or deployed as Page Type Defaults. Block content and display order can be controlled, as well as the ability to roll-back to previous versions of the stack.') . '</p>',
            ]),
            '/dashboard/blocks/types' => implode('', [
                '<p>' . t("All blocks currently installed on your site are listed here. Custom blocks that you've developed will appear under Awaiting Installation' until you install them. Click any block to find information about usage and refreshing a block's database tables.") . '</p>',
            ]),
            '/dashboard/system/environment/logging' => implode('', [
                '<p>' . t('View emails your site sends, as well as error messages PHP and MySQL errors returned as your site runs.') . '</p>',
            ]),
            '/dashboard/system/environment/debug' => implode('', [
                '<p>' . t('Choose between having error messages appear on the page where they occur (as you might want to do while developing a site) and hiding them from site visitors by saving them in your log (as you might want to do if your site is active).') . '</p>',
            ]),
            '/dashboard/system/environment/info' => implode('', [
                '<p>' . t("View information about your concrete5 site's configuration, as well as details about the hosting environment you're using.") . '</p>',
            ]),
            '/dashboard/system/attributes/types' => implode('', [
                '<p>' . t('Select which types of attributes to make available to pages, users, and files.') . '</p>',
            ]),
            '/dashboard/system/attributes/sets' => implode('', [
                '<p>' . t('Attributes are available to pages (aka Collections), users and files. If you want to add a new attribute to any of these objects, start here.') . '</p>',
            ]),
            '/dashboard/system/mail/importers' => implode('', [
                '<p>' . t('These scripts look for incoming mail to your site. Add-ons like the concrete5 Discussion Forum will attach emails to posts if they match, creating a new page containing the message as reply text.') . '</p>',
            ]),
            '/dashboard/system/mail/method' => implode('', [
                '<p>' . t('Set concrete5 to use an external SMTP server, instead of the default PHP Mail function, for sending email.') . '</p>',
            ]),
            '/dashboard/system/permissions/denylist' => implode('', [
                '<p>' . t('Block users at specific IP addresses from logging into your site. Blocked users can still view pages that are visible to guests.') . '</p>',
            ]),
            '/dashboard/system/permissions/tasks' => implode('', [
                '<p>' . t("Control users' ability to do perform specific tasks, such as install packages, alter permissions, etc.") . '</p>',
            ]),
            '/dashboard/system/permissions/file_types' => implode('', [
                '<p>' . t('View and change which file types you permit users to upload to your File Manager.') . '</p>',
            ]),
            '/dashboard/system/permissions/files' => implode('', [
                '<p>' . t("Control how users interact with your site's File Manager, allowing or disallowing actions like search, upload, replace and more.") . '</p>',
            ]),
            '/dashboard/system/permissions/trusted_proxies' => implode('', [
                '<p>' . t('If your website uses a reverse proxy (like <a href="%s" target="_blank">Cloudflare</a>), you have to specify here the list of the IP addresses of the proxy, so that concrete5 can detect the actual IP address of the visitors.', 'https://www.cloudflare.com/ips/') . '</p>',
                '<br /><br />',
                '<p>' . t('The checked headers will be trusted only when PHP detects that the connection is made via a trusted proxy.') . '</p>',
                '<br /><br />',
                '<p>' . t(/*%s is the name of an HTTP header*/'The %s header should be selected when using RFC 7239.', '<code>FORWARDED</code>') . '</p>',
                '<br /><br />',
                '<p>' . t('The other headers starting with %1$s are not standard but are widely used by popular reverse proxies (like %2$s).', '<code>X_...</code>', 'Apache mod_proxy/Amazon EC2') . '</p>',
            ]),
            '/dashboard/system/basics/editor' => implode('', [
                '<p>' . t("Control which set of tools the content-editor toolbar includes (e.g., Simple, Advanced, Office), and the toolbar's spatial dimensions.") . '</p>',
            ]),
            '/dashboard/system/basics/multilingual' => implode('', [
                '<p>' . t('View available language packs installed for the concrete5 Dashboard and editing interface.') . '</p>',
            ]),
            '/dashboard/system/basics/multilingual/update' => implode('', [
                '<p>' . t('Install new language files and update the outdated ones.<br />You can contribute to translations on %s', '<a href="https://translate.concrete5.org" target="_blank">translate.concrete5.org</a>.') . '</p>',
            ]),
            '/dashboard/system/basics/timezone' => implode('', [
                '<p>' . t('Allow your users to specify their time zone. This setting is editable in the user profile and in the dashboard users section.') . '</p>',
            ]),
            '/dashboard/system/basics/icons' => implode('', [
                '<p>' . t('Upload an image that will appear in the address bar when visitors go to your site and in the bookmark list if they bookmark it.') . '</p>',
            ]),
            '/dashboard/system/basics/name' => implode('', [
                '<p>' . t("Even if you change your website's logo, the Site Name is used in some emails.") . '</p>',
            ]),
            '/dashboard/system/registration/open' => implode('', [
                '<p>' . t('Enable Public Registration to let visitors create new user accounts.') . '</p>',
            ]),
            '/dashboard/system/registration/profiles' => implode('', [
                '<p>' . t("Display information about your concrete5 site's users, on a public page.") . '</p>',
            ]),
            '/dashboard/system/registration/postlogin' => implode('', [
                '<p>' . t('Determine where users should be redirected to after they login.') . '</p>',
            ]),
            '/dashboard/system/environment/storage' => implode('', [
                '<p>' . t("Create an alternate file-storage location (in addition to the standard file location) where you'll have the option of putting files after uploading them to the File Manager. ") . '</p>',
            ]),
            '/dashboard/system/update/update' => implode('', [
                '<p>' . t('Download the latest version of concrete5 and upgrade your site.') . '</p>',
            ]),
            '/dashboard/system/permissions/maintenance_mode' => implode('', [
                '<p>' . t('Enable or disable maintenance mode, in which your site is only visible to the admin user. Maintenance Mode is useful for developing, testing or temporarily disabling a site.') . '</p>',
            ]),
            '/dashboard/system/optimization/jobs' => implode('', [
                '<p>' . t('Have concrete5 perform various tasks to help your site running in top condition, process email posts, and update search engine indexing maps. Click the triangle icon next to the job to start it. A success message will be displayed once the job has been completed.') . '</p>',
            ]),
            '/dashboard/system/optimization/clearcache' => implode('', [
                '<p>' . t("If your site is behaving oddly or displaying out-of-date content, it's a good idea to clear the cache. If you're having to clear the cache a lot, you might want to just turn off caching in Cache & Speed Settings.") . '</p>',
            ]),
            '/dashboard/system/seo/urls' => [
                implode('', [
                    '<p>' . t("Remove index.php from your URLs with pretty URLs, and ensure canonical URLs if you're running a site at multiple domains.") . '</p>',
                ]),
                'dashboard-system-urls',
            ],
            '/dashboard/system/seo/codes' => implode('', [
                '<p>' . t("Add any HTML or Javascript code you need for analytics tracking to every page of your site, and pick whether it will go in pages' header or footer. This is where you would input code from Google Analytics, for example.") . '</p>',
            ]),
            '/dashboard/system/seo/statistics' => implode('', [
                '<p>' . t("Turns tracking of page views, file downloads and user registrations on or off. These are displayed on your site's Dashboard > Reports > Statistics page. If your high-traffic site experiences performance issues, you might consider disabling statistics tracking and investigate the use of an alternate, third-party solution for tracking site stats.") . '</p>',
            ]),
            '/dashboard/system/permissions/site' => implode('', [
                '<p>' . t("Control basic, general parameters for viewing and editing your site. Viewing Permissions makes your site's pages accessible to all users, registered-users-only or administrators-only. Edit Access controls which groups can edit pages, when logged in. For more granular control, set permissions on pages individually from Page Properties, or enable Advanced Permissions for even more granular control.") . '</p>',
            ]),
            '/dashboard/composer/edit' => implode('', [
                '<p>' . t("Any attributes or block areas you have enabled to be editable in Composer for this page type are available here. Add blocks to your page type's defaults. After adding the block, click it and choose the option to make it available in Composer.") . '</p>',
            ]),
            '/dashboard/users/attributes' => implode('', [
                '<p>' . t('Store data about your users-- like site preferences, birthdays, bios and more. Control which elements are available for users to update themselves.') . '</p>',
            ]),
            '/dashboard/users/add' => implode('', [
                '<p>' . t('Manually create new user accounts for your concrete5 site.') . '</p>',
            ]),
            '/dashboard' => implode('', [
                '<p>' . t('The Dashboard allows you to perform administrative tasks for your site.') . '</p>',
            ]),
            '/dashboard/system/files/image_uploading' => implode('', [
                '<p>' . t('Control maximum dimensions for all images uploaded to your website. Ensures that enormous images will be resized.') . ' ' . t('Auto-rotate images accordingly to EXIF metadata.') . '<br />' . t('Set PNG and JPEG compression options.') . '</p>',
            ]),
        ]);
    }
}
