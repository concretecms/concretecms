/**
 * Import and configure laravel mix.
 */
let mix = require('laravel-mix');
mix.webpackConfig({
    resolve: {
        symlinks: false
    },
    externals: {
        jquery: 'jQuery',
        bootstrap: true,
        vue: 'Vue',
        moment: 'moment'
    },
    // Override the default js compile settings to replace exclude with something that doesn't exclude node_modules.
    // @see node_modules/laravel-mix/src/components/JavaScript.js for the original
    module: {
        rules: [
            {
                test: /\.jsx?$/,
                exclude: /(bower_components|node_modules\/v-calendar)/,
                use: [
                    {
                        loader: 'babel-loader',
                        options: Config.babel()
                    }
                ]
            }
        ]
    }
});

mix.options({
    processCssUrls: false
});

mix.setPublicPath('../concrete');

/********************************************************/
/* IMPORTANT: when you add/remove a generated asset,    */
/* remember to update libraries/git-skip.js accordingly */
/********************************************************/

/**
 * Copy pre-minified assets.
 */
if (mix.inProduction()) {
    mix.copy('node_modules/vue/dist/vue.min.js', '../concrete/js/vue.js');
} else {
    mix.copy('node_modules/vue/dist/vue.js', '../concrete/js/vue.js');
}
mix.copy('node_modules/jquery/dist/jquery.min.js', '../concrete/js/jquery.js');
mix.copy('node_modules/@fortawesome/fontawesome-free/webfonts', '../concrete/css/webfonts');
mix.copy('node_modules/@fortawesome/fontawesome-free/css/all.css', '../concrete/css/fontawesome/all.css');
mix.copy('node_modules/bootstrap/dist/js/bootstrap.bundle.min.js', '../concrete/js/bootstrap.js');
mix.copy('node_modules/ckeditor4', '../concrete/js/ckeditor');
mix.copy('node_modules/ace-builds/src-min', '../concrete/js/ace');

// Copy Bedrock assets so that themes can include them for style customization, etc...
if (mix.inProduction()) {
    // Note: this should only copy SCSS assets if possible, because the only reason we're copying them is because
    // we need to include them in our theme customizer.

    // Bootstrap SCSS
    mix.copy('node_modules/bootstrap/scss', '../concrete/bedrock/assets/bootstrap/scss');

    // Bedrock
    mix.copy('node_modules/@concretecms/bedrock/assets/account/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/account/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/basics/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/basics/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/bedrock/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/bedrock/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/boards/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/boards/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/calendar/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/calendar/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/ckeditor/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/ckeditor/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/cms/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/cms/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/conversations/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/conversations/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/desktop/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/desktop/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/documents/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/documents/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/express/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/express/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/faq/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/faq/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/imagery/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/imagery/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/maps/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/maps/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/multilingual/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/multilingual/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/navigation/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/navigation/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/polls/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/polls/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/profile/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/profile/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/search/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/search/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/social/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/social/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/taxonomy/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/taxonomy/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/testimonials/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/testimonials/scss');
    mix.copy('node_modules/@concretecms/bedrock/assets/video/scss', '../concrete/bedrock/assets/@concretecms/bedrock/assets/video/scss');

    // Font Awesome
    mix.copy('node_modules/@fortawesome/fontawesome-free/less', '../concrete/bedrock/assets/@fortawesome/fontawesome-free/less');
    mix.copy('node_modules/@fortawesome/fontawesome-free/metadata', '../concrete/bedrock/assets/@fortawesome/fontawesome-free/metadata');
    mix.copy('node_modules/@fortawesome/fontawesome-free/scss', '../concrete/bedrock/assets/@fortawesome/fontawesome-free/scss');

    // Fullcalendar
    mix.copy('node_modules/fullcalendar/dist', '../concrete/bedrock/assets/fullcalendar/dist');
}

/**
 * Build shared assets
 */
// Fullcalendar
mix
    .copy('node_modules/fullcalendar/dist/fullcalendar.min.css', '../concrete/css/fullcalendar.css')
    .js('node_modules/@concretecms/bedrock/assets/calendar/js/vendor/fullcalendar.js', 'js/fullcalendar.js');

// CKEditor
mix
    .sass('node_modules/@concretecms/bedrock/assets/ckeditor/scss/concrete.scss', 'css/ckeditor/concrete.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })
    .js('node_modules/@concretecms/bedrock/assets/ckeditor/js/concrete.js', 'js/ckeditor/concrete.js');

// TUI Image Editor
mix
    .js('assets/tui-image-editor/tui-image-editor.js', 'js/tui-image-editor.js')
    .sass('assets/tui-image-editor/tui-image-editor.scss', 'css/tui-image-editor.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    });


/**
 * Build Block Components
 */
mix.js('assets/blocks/gallery/gallery.js', '../concrete/blocks/gallery/auto.js');
mix.js('assets/blocks/accordion/accordion.js', '../concrete/blocks/accordion/auto.js');


/**
 * Build accessory Features
 */
mix
    .sass('node_modules/@concretecms/bedrock/assets/account/scss/frontend.scss', 'css/features/account/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })
    .js('node_modules/@concretecms/bedrock/assets/account/js/frontend.js', 'js/features/account/frontend.js');

mix
    .sass('node_modules/@concretecms/bedrock/assets/profile/scss/frontend.scss', 'css/features/profile/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })

mix
    .sass('node_modules/@concretecms/bedrock/assets/boards/scss/frontend.scss', 'css/features/boards/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })
    .js('node_modules/@concretecms/bedrock/assets/boards/js/frontend.js', 'js/features/boards/frontend.js');

mix
    .js('node_modules/@concretecms/bedrock/assets/navigation/js/frontend.js', 'js/features/navigation/frontend.js')
    .sass('node_modules/@concretecms/bedrock/assets/navigation/scss/frontend.scss', 'css/features/navigation/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    });

mix
    .sass('node_modules/@concretecms/bedrock/assets/search/scss/frontend.scss', 'css/features/search/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })

mix
    .sass('node_modules/@concretecms/bedrock/assets/faq/scss/frontend.scss', 'css/features/faq/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })

mix
    .sass('node_modules/@concretecms/bedrock/assets/imagery/scss/frontend.scss', 'css/features/imagery/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })
    .js('node_modules/@concretecms/bedrock/assets/imagery/js/frontend.js', 'js/features/imagery/frontend.js');

mix
    .sass('node_modules/@concretecms/bedrock/assets/calendar/scss/frontend.scss', 'css/features/calendar/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })
    .js('node_modules/@concretecms/bedrock/assets/calendar/js/frontend.js', 'js/features/calendar/frontend.js');

mix
    .sass('node_modules/@concretecms/bedrock/assets/conversations/scss/frontend.scss', 'css/features/conversations/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })
    .js('node_modules/@concretecms/bedrock/assets/conversations/js/frontend.js', 'js/features/conversations/frontend.js');

mix
    .sass('node_modules/@concretecms/bedrock/assets/documents/scss/frontend.scss', 'css/features/documents/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })
    .js('node_modules/@concretecms/bedrock/assets/documents/js/frontend.js', 'js/features/documents/frontend.js');

mix
    .sass('node_modules/@concretecms/bedrock/assets/basics/scss/frontend.scss', 'css/features/basics/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })

mix
    .sass('node_modules/@concretecms/bedrock/assets/video/scss/frontend.scss', 'css/features/video/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })

mix
    .sass('node_modules/@concretecms/bedrock/assets/taxonomy/scss/frontend.scss', 'css/features/taxonomy/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })

mix
    .sass('node_modules/@concretecms/bedrock/assets/express/scss/frontend.scss', 'css/features/express/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })
    .js('node_modules/@concretecms/bedrock/assets/express/js/frontend.js', 'js/features/express/frontend.js');

mix
    .js('node_modules/@concretecms/bedrock/assets/multilingual/js/frontend.js', 'js/features/multilingual/frontend.js')
    .sass('node_modules/@concretecms/bedrock/assets/multilingual/scss/frontend.scss', 'css/features/multilingual/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    });

mix
    .sass('node_modules/@concretecms/bedrock/assets/maps/scss/frontend.scss', 'css/features/maps/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })
    .js('node_modules/@concretecms/bedrock/assets/maps/js/frontend.js', 'js/features/maps/frontend.js');

mix
    .sass('node_modules/@concretecms/bedrock/assets/testimonials/scss/frontend.scss', 'css/features/testimonials/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })

mix
    .sass('node_modules/@concretecms/bedrock/assets/social/scss/frontend.scss', 'css/features/social/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })

mix
    .sass('node_modules/@concretecms/bedrock/assets/polls/scss/frontend.scss', 'css/features/polls/frontend.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })

// The CMS entry point
mix
    .sass('assets/cms.scss', 'css/cms.css', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })
    .js('assets/cms.js', 'js/cms.js');

// Elemental Theme
mix.js('assets/themes/elemental/js/main.js', 'themes/elemental');

// Atomik Theme
mix
    .sass('../concrete/themes/atomik/skins/default/scss/main.scss', 'themes/atomik/skins/default/', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })
    .js('assets/themes/atomik/js/main.js', 'themes/atomik');
// Dashboard Theme
mix
    .sass('assets/themes/dashboard/scss/main.scss', 'themes/dashboard', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })
    .js('assets/themes/dashboard/js/main.js', 'themes/dashboard');

/**
 * Build core themes
 */
// Concrete Theme
mix
    .sass('assets/themes/concrete/scss/main.scss', 'themes/concrete', {
        sassOptions: {
            includePaths: [
                path.resolve(__dirname, './node_modules/')
            ]
        }
    })
    .js('assets/themes/concrete/js/main.js', 'themes/concrete');

/**
 * Copy bedrock SVGs into our repository
 */
mix.copy('node_modules/@concretecms/bedrock/assets/icons/sprites.svg', '../concrete/images/icons/bedrock/sprites.svg');

/**
 * Copy jquery ui icons into our repository
 */
mix.copy('node_modules/jquery-ui/themes/base/images/ui-*', '../concrete/images/');

/**
 * Turn off notifications
 */
mix
    .disableNotifications()
    .options({
        clearConsole: false,
        // Disable extracting licenses from comments
        terser: {
            extractComments: false,
        }
    })
