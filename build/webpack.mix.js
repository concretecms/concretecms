let mix = require('laravel-mix');

// Set our public path
mix.setPublicPath('../concrete/js');

// Build our components into a bundle
mix.js('components/avatar.js', 'components/avatar.bundle.js');

// Turn off notifications
mix
    .disableNotifications()
    .options({
        clearConsole: false,
    })
