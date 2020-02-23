module.exports = function(grunt) {
    var fs = require('fs');
    var path = require('path');
    var extend = require('util')._extend;

    var parameters = null;
    if(fs.existsSync(__dirname + '/Gruntfile.parameters.js')) {
        parameters = require(__dirname + '/Gruntfile.parameters.js');
    }
    parameters = parameters || {};
    grunt.option.flags().forEach(function(p) {
        var m = /^--(.+?)=(.+)$/.exec(p);
        if(m) {
            parameters[m[1]] = m[2];
        }
    });

    var config = {};

    config.DIR_REL = ('DIR_REL' in parameters) ? parameters.DIR_REL : '';
    config.DIR_BASE = ('DIR_BASE' in parameters) ? parameters.DIR_BASE : path.join(__dirname, '..');

    // Options for the tool that will merge the files
    var concatOptions = {
        separator: ''
    };

    // List of the files to be merged
    var concat = {
    };

    // Options for the generation of JavaScripts. See https://github.com/gruntjs/grunt-contrib-uglify
    var jsOptions = {
    };

    // List of the JavaScripts to be generated
    var js = {


    };

    // Options for the generation of the CSS files. See https://github.com/gruntjs/grunt-contrib-less
    var cssOptions = {
    };

    // List of the CSS files to be generated
    // Note –bootstrap dies in here when attempting to be built with grunt css; something about
    // its minified syntax being included within the other app.less file. So if you need to build
    // bootstrap uncomment this, run grunt css:debug, get it working, then comment the line back out
    // and run grunt css
    var css = {

    };

    // Let's include the dependencies
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-newer');

    // Now let's build the final configuration for Grunt.

    // Let's define the concat section (for concatenating giles)
    config.concat = extend({options: concatOptions}, concat);

    // Let's define the uglify section (for generating JavaScripts)
    var jsTargets = {release: [], debug: []};
    for(var concatKey in concat) {
        if(concat[concatKey].beforeJS) {
            jsTargets.release.push('concat:' + concatKey);
            jsTargets.debug.push('newer:concat:' + concatKey);
        }
    }



    // Prepare env
    config.env = {
        dev: {
            NODE_ENV: 'development'
        },
        prod: {
            NODE_ENV: 'production'
        }
    };

    var configFactory = function(watch) {

        var config = require('laravel-mix/setup/webpack.config');

        return config;
    };

    // Prepare webpack configuration, the config needs to be loaded in at the very last second
    config.webpack = {
        prod: function() {
            console.log('building for prod');
            return configFactory();
        },
        dev: function() {
            return configFactory();
        },
        watch: function() {
            var config =  configFactory();
            config.watch = true;
            return config;
        }
    };

    config["webpack-dev-server"] = {
        dev: function() {
            var config = configFactory();
            return config;
        }
    }


    var watchJS = [];
    var watchCSS = [];

    config.uglify = {options: jsOptions};
    for(var key in js) {
        var target = {files: {}};
        target.files[js[key].dest] = js[key].src;

        var srcFile = js[key].src;
        if (typeof(srcFile) == 'string') {
            watchJS.push(srcFile);
        } else {
            for (i = 0; i < srcFile.length; i++) {
                watchJS.push(srcFile[i]);
            }
        }

        config.uglify[key + '_release'] = extend({options: {compress: {warnings: false}}}, target);

        jsTargets.release.push('uglify:' + key + '_release');
        target.options = {compress: {warnings: true}};
        target.options.mangle = false;
        target.options.sourceMap = true;
        config.uglify[key + '_debug'] = target;
        jsTargets.debug.push('newer:uglify:' + key + '_debug');
    }


    // Append webpack steps
    jsTargets.release.push('env:prod');
    jsTargets.release.push('webpack:prod');
    jsTargets.debug.push('env:dev');
    jsTargets.debug.push('webpack:dev');


    // Let's define the less section (for generating CSS files)
    config.less = {
        options: cssOptions,
        debug: {
            options: {
                compress: false,
                yuicompress: false
            },
            files: css
        },
        release: {
            options: {
                compress: true,
                yuicompress: true
            },
            files: css
        }
    };


    config.watch = {
        javascript: {
            files: watchJS,
            tasks: ['js:debug'],
            options: {
                livereload: true
            }
        },

        css: {
            files: '<%=DIR_BASE%>/concrete/css/**/*.less',
            tasks: ['css:debug'],
            options: {
                livereload: true
            }
        }
    };

    config.jshint = {
        options: {
        },
        all: [
            '<%=DIR_BASE%>/concrete/js/build/core/**/*.js',
            '!<%=DIR_BASE%>/concrete/js/build/core/image-editor/build/**/*.js',
            '!<%=DIR_BASE%>/concrete/js/build/core/app/json.js',
        ]
    };

    // Set Grunt tasks
    grunt.initConfig(config);

    grunt.registerTask('generate-constants', 'Generate Javascript Constants', function() {
        require('./tasks/generate-constants.js')(grunt, config, parameters, this.async());
    });

    grunt.registerTask('gitskip-on:js', 'Force GIT to consider built JS assets as unchanged', function() {
        require('./tasks/git-skipper.js')(grunt, config, parameters, 'js', true, this.async());
    });
    grunt.registerTask('gitskip-off:js', 'Allow GIT to consider built JS assets', function() {
        require('./tasks/git-skipper.js')(grunt, config, parameters, 'js', false, this.async());
    });
    grunt.registerTask('gitskip-on:css', 'Force GIT to consider built CSS assets as unchanged', function() {
        require('./tasks/git-skipper.js')(grunt, config, parameters, 'css', true, this.async());
    });
    grunt.registerTask('gitskip-off:css', 'Allow GIT to consider built CSS assets', function() {
        require('./tasks/git-skipper.js')(grunt, config, parameters, 'css', false, this.async());
    });
    grunt.registerTask('gitskip-on', 'Force GIT to consider built CSS/JS assets as unchanged', function() {
        require('./tasks/git-skipper.js')(grunt, config, parameters, 'all', true, this.async());
    });
    grunt.registerTask('gitskip-off', 'Allow GIT to consider built CSS/JS assets', function() {
        require('./tasks/git-skipper.js')(grunt, config, parameters, 'all', false, this.async());
    });

    grunt.registerTask('webpack:hot', 'Hot reload webpack build', [
        'env:dev',
        'webpack-dev-server:dev'
    ])

    grunt.loadNpmTasks('grunt-env');
    grunt.loadNpmTasks('grunt-webpack');

    grunt.registerTask('jsOnly:debug', jsTargets.debug);
    grunt.registerTask('jsOnly:release', jsTargets.release);

    //grunt.registerTask('js:debug', ['generate-constants', 'jsOnly:debug' ]);
    //grunt.registerTask('js:release', ['generate-constants', 'jsOnly:release' ]);
    grunt.registerTask('js:debug', ['jsOnly:debug', 'gitskip-on:js']);
    grunt.registerTask('js:release', ['jsOnly:release', 'gitskip-off:js']);
    grunt.registerTask('js', 'js:release');
    grunt.registerTask('js:check', ['concat:image_editor', 'jshint:all']);

    grunt.registerTask('css:debug', ['less:debug', 'gitskip-on:css']);
    grunt.registerTask('css:release', ['less:release', 'gitskip-off:css']);
    grunt.registerTask('css', 'css:release');

    grunt.registerTask('debug', ['js:debug', 'css:debug']);
    grunt.registerTask('release', ['js:release', 'css:release']);
    grunt.registerTask('debug', ['js:debug', 'css:debug']);

    grunt.registerTask('remove-short-tags', 'Remove short tags.', function() {
        require('./tasks/remove-short-tags.js')(grunt, config, parameters, this.async());
    });

    grunt.registerTask('build-release-download', 'Build process: download the latest concrete5 release from GitHub.', function() {
        require('./tasks/build-release/download.js')(grunt, config, parameters, this.async());
    });

    grunt.registerTask('build-release-build', 'Build process: compile the required files of a clean concrete5 installation.', function() {
        require('./tasks/build-release/build.js')(grunt, config, parameters, this.async());
    });

    grunt.registerTask('build-release-clean', 'Build process: remove useless files and folders.', function() {
        require('./tasks/build-release/clean.js')(grunt, config, parameters, this.async());
    });

    grunt.registerTask('build-release-remove-short-tags', 'Build process: remove short tags.', function() {
        require('./tasks/build-release/remove-short-tags.js')(grunt, config, parameters, this.async());
    });

    grunt.registerTask('build-release-translations', 'Build process: downloading Translations.', function() {
        require('./tasks/build-release/translations.js')(grunt, config, parameters, this.async());
    });

    grunt.registerTask('build-release-create-zip', 'Build process: create zip file.', function() {
        require('./tasks/build-release/create-zip.js')(grunt, config, parameters, this.async());
    });

    grunt.registerTask('build-release-cleanup', 'Build process: cleanup.', function() {
        require('./tasks/build-release/cleanup.js')(grunt, config, parameters, this.async());
    });

    grunt.registerTask(
        'build-release',
        [
            'build-release-download',
            'build-release-build',
            'build-release-clean',
            'build-release-remove-short-tags',
            //'build-release-translations',
            'build-release-create-zip',
            'build-release-cleanup'
        ]
    );

    grunt.registerTask('default', 'release');
};
