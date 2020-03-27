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
    .sass('assets/bedrock/ckeditor/scss/concrete.scss', 'css/ckeditor/concrete.css')
    .js('assets/bedrock/ckeditor/js/concrete.js', 'js/ckeditor/concrete.js');

// The CMS entry point
mix
    .sass('assets/cms.scss', 'css/cms.css')
    .js('assets/cms.js', 'js/cms.js');

/**
 * Build core themes
 */
// Concrete Theme
mix
    .sass('assets/themes/concrete/scss/main.scss', 'themes/concrete/main.css')
    .js('assets/themes/concrete/js/main.js', 'themes/concrete/main.js');

// Elemental Theme
mix
    .sass('assets/themes/elemental/scss/main.scss', 'themes/elemental/main.css')
    .js('assets/themes/elemental/js/main.js', 'themes/elemental/main.js');


// Dashboard Theme
mix
    .sass('assets/themes/dashboard/scss/main.scss', 'themes/dashboard/main.css')
    .js('assets/themes/dashboard/js/main.js', 'themes/dashboard/main.js');

/**
 * Build accessory Features
 */
mix
    .sass('assets/bedrock/boards/scss/frontend.scss', 'css/features/boards/frontend.css')
    .js('assets/bedrock/boards/js/frontend.js', 'js/features/boards/frontend.js');

mix
    .js('assets/bedrock/navigation/js/frontend.js', 'js/features/navigation/frontend.js')
    .sass('assets/bedrock/navigation/scss/frontend.scss', 'css/features/navigation/frontend.css');

mix
    .sass('assets/bedrock/search/scss/frontend.scss', 'css/features/search/frontend.css')

mix
    .sass('assets/bedrock/faq/scss/frontend.scss', 'css/features/faq/frontend.css')

mix
    .sass('assets/bedrock/imagery/scss/frontend.scss', 'css/features/imagery/frontend.css')
    .js('assets/bedrock/imagery/js/frontend.js', 'js/features/imagery/frontend.js');

mix
    .sass('assets/bedrock/calendar/scss/frontend.scss', 'css/features/calendar/frontend.css')
    .js('assets/bedrock/calendar/js/frontend.js', 'js/features/calendar/frontend.js');

mix
    .sass('assets/bedrock/conversations/scss/frontend.scss', 'css/features/conversations/frontend.css')
    .js('assets/bedrock/conversations/js/frontend.js', 'js/features/conversations/frontend.js');

mix
    .sass('assets/bedrock/documents/scss/frontend.scss', 'css/features/documents/frontend.css')
    .js('assets/bedrock/documents/js/frontend.js', 'js/features/documents/frontend.js');

mix
    .sass('assets/bedrock/basics/scss/frontend.scss', 'css/features/basics/frontend.css')

mix
    .sass('assets/bedrock/video/scss/frontend.scss', 'css/features/video/frontend.css')

mix
    .sass('assets/bedrock/taxonomy/scss/frontend.scss', 'css/features/taxonomy/frontend.css')

mix
    .sass('assets/bedrock/express/scss/frontend.scss', 'css/features/express/frontend.css')
    .js('assets/bedrock/express/js/frontend.js', 'js/features/express/frontend.js');

mix
    .js('assets/bedrock/multilingual/js/frontend.js', 'js/features/multilingual/frontend.js')
    .sass('assets/bedrock/multilingual/scss/frontend.scss', 'css/features/multilingual/frontend.css');

mix
    .sass('assets/bedrock/maps/scss/frontend.scss', 'css/features/maps/frontend.css')
    .js('assets/bedrock/maps/js/frontend.js', 'js/features/maps/frontend.js');

mix
    .sass('assets/bedrock/testimonials/scss/frontend.scss', 'css/features/testimonials/frontend.css')

mix
    .sass('assets/bedrock/social/scss/frontend.scss', 'css/features/social/frontend.css')

mix
    .sass('assets/bedrock/polls/scss/frontend.scss', 'css/features/polls/frontend.css')


/**
 * Turn off notifications
 */
mix
    .disableNotifications()
    .options({
        clearConsole: false,
    })
