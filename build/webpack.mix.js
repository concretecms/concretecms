/**
 * Import and configure laravel mix.
 */
let mix = require('laravel-mix');
mix.webpackConfig({
    externals: {
        jquery: 'jQuery'
    }
});
mix.options({
    processCssUrls: false
});
mix.setPublicPath('../concrete');

/**
 * Copy pre-minified assets.
 */
mix.copy('node_modules/jquery/dist/jquery.min.js', '../concrete/js/jquery.js');
mix.copy('node_modules/@fortawesome/fontawesome-free/webfonts', '../concrete/css/webfonts');
mix.copy('node_modules/@fortawesome/fontawesome-free/css/all.css', '../concrete/css/fontawesome/all.css');
mix.copy('node_modules/ckeditor4', '../concrete/js/ckeditor');

/**
 * Build shared assets
 */
// CKEditor
mix
    .sass('node_modules/concretecms-bedrock/assets/ckeditor/scss/concrete.scss', 'css/ckeditor/concrete.css')
    .js('node_modules/concretecms-bedrock/assets/ckeditor/js/concrete.js', 'js/ckeditor/concrete.js');

// The CMS entry point
mix
    .sass('assets/cms.scss', 'css/cms.css')
    .js('assets/cms.js', 'js/cms.js');

/**
 * Build core themes
 */
// Concrete Theme
mix
    .sass('assets/themes/concrete/scss/main.scss', 'themes/concrete')
    .js('assets/themes/concrete/js/main.js', 'themes/concrete');

// Elemental Theme
mix
    .sass('assets/themes/elemental/scss/main.scss', 'themes/elemental')
    .js('assets/themes/elemental/js/main.js', 'themes/elemental');


// Dashboard Theme
mix
    .sass('assets/themes/dashboard/scss/main.scss', 'themes/dashboard')
    .js('assets/themes/dashboard/js/main.js', 'themes/dashboard');

/**
 * Build accessory Features
 */
// Pages
mix
    .sass('node_modules/concretecms-bedrock/assets/navigation/scss/frontend.scss', 'css/features/navigation/frontend.css')

// Imagery
mix
    .sass('node_modules/concretecms-bedrock/assets/imagery/scss/frontend.scss', 'css/features/imagery/frontend.css')
    .js('node_modules/concretecms-bedrock/assets/imagery/js/frontend.js', 'js/features/imagery/frontend.js');

// Calendar
mix
    .sass('node_modules/concretecms-bedrock/assets/calendar/scss/frontend.scss', 'css/features/calendar/frontend.css')
    .js('node_modules/concretecms-bedrock/assets/calendar/js/frontend.js', 'js/features/calendar/frontend.js');

// Conversations
mix
    .sass('node_modules/concretecms-bedrock/assets/conversations/scss/frontend.scss', 'css/features/conversations/frontend.css')
    .js('node_modules/concretecms-bedrock/assets/conversations/js/frontend.js', 'js/features/conversations/frontend.js');

// Vidieo
mix
    .sass('node_modules/concretecms-bedrock/assets/video/scss/frontend.scss', 'css/features/video/frontend.css')
    .js('node_modules/concretecms-bedrock/assets/video/js/frontend.js', 'js/features/video/frontend.js');


/**
 * Turn off notifications
 */
mix
    .disableNotifications()
    .options({
        clearConsole: false,
    })
