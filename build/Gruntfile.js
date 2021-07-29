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
   
    // Set Grunt tasks
    grunt.initConfig(config);

    grunt.registerTask('generate-constants', 'Generate Javascript Constants', function() {
        require('./tasks/generate-constants.js')(grunt, config, parameters, this.async());
    });

    grunt.loadNpmTasks('grunt-env');

    grunt.registerTask('build-release-download', 'Build process: download the latest Concrete release from GitHub.', function() {
        require('./tasks/build-release/download.js')(grunt, config, parameters, this.async());
    });

    grunt.registerTask('build-release-build', 'Build process: compile the required files of a clean Concrete installation.', function() {
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
            'build-release-create-zip',
            'build-release-cleanup'
        ]
    );
};
